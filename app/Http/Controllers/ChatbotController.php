<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected array $chatModels = [
        'gemini-2.5-flash',
        'gemma-3-1b-it',
    ];

    protected function generateContent(string $apiKey, array $payload): array
    {
        $lastData = [];

        foreach ($this->chatModels as $model) {
            $response = Http::timeout(25)
                ->acceptJson()
                ->asJson()
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey,
                    $payload
                );

            $data = $response->json();
            $reply = is_array($data) ? $this->parseGeminiReply($data) : null;

            if ($response->successful() && $reply !== null) {
                return [
                    'reply' => $reply,
                    'model' => $model,
                    'data'  => $data,
                ];
            }

            $lastData = is_array($data) ? $data : ['error' => ['message' => 'Unknown Gemini response']];

            if (($lastData['error']['status'] ?? null) !== 'UNAVAILABLE') {
                break;
            }
        }

        return [
            'reply' => null,
            'model' => null,
            'data'  => $lastData,
        ];
    }

    protected function buildGeminiContents(array $history, string $message): array
    {
        $contents = [];

        foreach ($history as $item) {
            $role = ($item['role'] ?? 'user') === 'model' ? 'model' : 'user';
            $text = trim((string) ($item['text'] ?? ''));

            if ($text === '') {
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $text],
                ],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $message],
            ],
        ];

        return $contents;
    }

    protected function parseGeminiReply(array $data): ?string
    {
        $parts = $data['candidates'][0]['content']['parts'] ?? [];

        if (!is_array($parts) || $parts === []) {
            return null;
        }

        $texts = [];

        foreach ($parts as $part) {
            $text = trim((string) ($part['text'] ?? ''));

            if ($text !== '') {
                $texts[] = $text;
            }
        }

        return $texts === [] ? null : implode("\n", $texts);
    }

    protected function providerErrorReply(array $data, string $message): string
    {
        $status = $data['error']['status'] ?? null;

        if ($status === 'RESOURCE_EXHAUSTED') {
            return 'Gemini API đã nhận request nhưng tài khoản hiện đã hết quota hoặc chưa được cấp quota. Bạn kiểm tra billing, hạn mức hoặc thử lại sau.';
        }

        if ($status === 'PERMISSION_DENIED') {
            return 'Gemini API key chưa hợp lệ hoặc project chưa được cấp quyền dùng Gemini API. Bạn kiểm tra lại API key và cấu hình project trên Google AI Studio.';
        }

        return $this->fallbackReply($message);
    }

    protected function fallbackReply(string $message): string
    {
        $normalized = mb_strtolower(trim($message));

        if ($normalized === '') {
            return 'Bạn hãy nhập câu hỏi về thực đơn, đặt bàn, giờ mở cửa hoặc liên hệ để tôi hỗ trợ.';
        }

        if (str_contains($normalized, 'thuc don') || str_contains($normalized, 'thực đơn') || str_contains($normalized, 'menu') || str_contains($normalized, 'món')) {
            return 'Bạn có thể xem thực đơn tại trang Menu của nhà hàng. Nếu muốn, hãy nói tên món hoặc loại món để tôi gợi ý nhanh hơn.';
        }

        if (str_contains($normalized, 'dat ban') || str_contains($normalized, 'đặt bàn') || str_contains($normalized, 'reservation') || str_contains($normalized, 'book')) {
            return 'Để đặt bàn, bạn vào trang Đặt bàn, chọn thời gian, số lượng khách và điền thông tin liên hệ. Nếu cần, tôi có thể hướng dẫn từng bước.';
        }

        if (str_contains($normalized, 'gio mo cua') || str_contains($normalized, 'giờ mở cửa') || str_contains($normalized, 'mấy giờ') || str_contains($normalized, 'open')) {
            return 'Bạn vui lòng xem mục liên hệ hoặc phần thông tin trên trang chủ để kiểm tra giờ mở cửa mới nhất của nhà hàng.';
        }

        if (str_contains($normalized, 'lien he') || str_contains($normalized, 'liên hệ') || str_contains($normalized, 'so dien thoai') || str_contains($normalized, 'số điện thoại') || str_contains($normalized, 'dia chi') || str_contains($normalized, 'địa chỉ')) {
            return 'Bạn có thể xem thông tin liên hệ trực tiếp trên website của nhà hàng. Nếu cần, tôi có thể hướng dẫn bạn tới đúng trang liên hệ hoặc đặt bàn.';
        }

        return 'Hiện AI nâng cao chưa được cấu hình trên môi trường này, nhưng tôi vẫn có thể hỗ trợ các câu hỏi cơ bản về thực đơn, đặt bàn, giờ mở cửa và liên hệ.';
    }

    public function testAI()
    {
        $apiKey = trim((string) env('GEMINI_API_KEY'));

        if ($apiKey === '') {
            return response()->json([
                'error' => 'Thiếu GEMINI_API_KEY trong file .env',
            ], 500);
        }

        return response()->json(
            $this->generateContent($apiKey, [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => 'Bạn là trợ lý AI của nhà hàng. Trả lời ngắn gọn, hữu ích, bằng tiếng Việt.'],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => 'Xin chào'],
                        ],
                    ],
                ],
            ])
        );
    }

    public function ask(Request $request)
    {
        $message = trim((string) $request->input('message'));
        $history = $request->input('history', []);
        $apiKey  = trim((string) env('GEMINI_API_KEY'));

        if ($message === '') {
            return response()->json([
                'reply' => $this->fallbackReply($message),
            ]);
        }

        if ($apiKey === '') {
            return response()->json([
                'reply'    => $this->fallbackReply($message),
                'fallback' => true,
            ]);
        }

        // 1. Lấy dữ liệu thực đơn từ bảng 'menus'
        $menus = \Illuminate\Support\Facades\DB::table('menus')
            ->where('status', 1)
            ->get(['id', 'name', 'price', 'description', 'image']);

        // 2. Chuyển đổi dữ liệu thành chuỗi văn bản cho AI đọc
        $menuContext = "DANH SÁCH THỰC ĐƠN CỦA NHÀ HÀNG (Giá VNĐ):\n";
        foreach ($menus as $item) {
            $priceFormatted = number_format($item->price, 0, ',', '.');
            $desc = $item->description ? " - " . $item->description : "";
            
            // Lấy tên file ảnh và tạo link hình ảnh
            $fileName = basename($item->image); 
            $imageUrl = asset('images/' . $fileName); 
            
            // Tạo link dẫn tới trang chi tiết của món ăn
            // LƯU Ý QUAN TRỌNG: Hãy đảm bảo '/mon-an/' khớp với route thực tế trên website của bạn
            $detailUrl = url('/chi-tiet-mon-an/' . $item->id); 
            
            $menuContext .= "- Món {$item->name}: {$priceFormatted}đ{$desc} | Link ảnh: {$imageUrl} | Link chi tiết: {$detailUrl}\n";
        }

        // 3. Tạo System Prompt (HƯỚNG DẪN BOT HIỂN THỊ LINK BẤM CÙNG VỚI ẢNH)
        // 3. Tạo System Prompt (HƯỚNG DẪN BOT CHUYỂN HƯỚNG ĐẶT BÀN)
        $systemPrompt = "Bạn là trợ lý AI lễ tân của nhà hàng cao cấp Aurora Garden. "
            . "Hãy trả lời bằng tiếng Việt, lịch sự, thân thiện và đúng trọng tâm. "
            . "BẮT BUỘC phải dựa vào danh sách thực đơn dưới đây để tư vấn cho khách. "
            . "TUYỆT ĐỐI KHÔNG TỰ BỊA RA MÓN ĂN NGOÀI DANH SÁCH NÀY. Nếu khách hỏi món không có, hãy xin lỗi và gợi ý món khác.\n\n"
            . "🌟 QUAN TRỌNG SỐ 1: Khi khách yêu cầu xem một món ăn, bạn PHẢI làm 2 việc:\n"
            . "1. Hiển thị hình ảnh của món đó bằng cú pháp Markdown: ![Tên món](Link ảnh)\n"
            . "2. Cung cấp đường link để khách click vào xem chi tiết bằng cú pháp Markdown: [👉 Xem chi tiết và Đặt món](Link chi tiết)\n\n"
            . "🌟 QUAN TRỌNG SỐ 2 (ĐẶT BÀN): Khi khách hàng có nhu cầu đặt bàn (Ví dụ: 'tôi muốn đặt bàn', 'cho tôi đặt chỗ', 'đặt bàn như nào'), bạn BẮT BUỘC phải cung cấp đường link dẫn tới trang đặt bàn chính thức của nhà hàng bằng cú pháp Markdown: [👉 Nhấp vào đây để Chọn bàn và Đặt chỗ](/reservation)\n"
            . "Ví dụ cách trả lời:\n"
            . "Dạ vâng, Aurora Garden rất hân hạnh được đón tiếp quý khách. Để chọn vị trí ngồi ưng ý và đặt trước món ăn, quý khách vui lòng thao tác trực tiếp tại đây nhé:\n"
            . "[👉 Nhấp vào đây để Chọn bàn và Đặt chỗ](/reservation)\n\n"
            . $menuContext;

        // 4. Gửi lên Gemini
        $result = $this->generateContent($apiKey, [
            'systemInstruction' => [
                'parts' => [
                    [
                        'text' => $systemPrompt,
                    ],
                ],
            ],
            'contents' => $this->buildGeminiContents(is_array($history) ? $history : [], $message),
        ]);

        if ($result['reply'] !== null) {
            return response()->json([
                'reply' => $result['reply'],
                'model' => $result['model'],
            ]);
        }

        return response()->json([
            'reply'          => is_array($result['data']) ? $this->providerErrorReply($result['data'], $message) : $this->fallbackReply($message),
            'fallback'       => true,
            'provider_error' => $result['data'],
        ]);
    }
}