<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

/**
 * Bài viết MẪU để xem giao diện. Chạy nhiều lần không bị trùng
 * (dựa vào slug) và không xóa dữ liệu có sẵn.
 * Nội dung chỉ là ví dụ, bạn sửa lại theo nhà hàng của mình.
 */
class NewsSeeder extends Seeder
{
    public function run()
    {
        $posts = [
            [
                'slug'     => 'chao-mung-ban-den-voi-aurora-garden',
                'title'    => 'Chào mừng bạn đến với Aurora Garden',
                'category' => 'Tin nhà hàng',
                'days_ago' => 1,
                'excerpt'  => 'Không gian ấm cúng, thực đơn chuẩn vị và dịch vụ đặt bàn trực tuyến tiện lợi đang chờ bạn.',
                'content'  => "Aurora Garden mang đến một không gian ấm cúng để bạn quây quần bên gia đình và bạn bè, cùng những món ăn được chế biến từ nguyên liệu tươi mỗi ngày.\n\nBạn có thể xem thực đơn, chọn trước món ăn và đặt bàn ngay trên website, tiết kiệm thời gian chờ đợi khi đến nhà hàng.\n\nChúng tôi luôn mong được phục vụ bạn.",
            ],
            [
                'slug'     => 'dat-ban-online-tien-loi-hon',
                'title'    => 'Đặt bàn online: nhanh gọn, giữ chỗ chắc chắn',
                'category' => 'Tin nhà hàng',
                'days_ago' => 3,
                'excerpt'  => 'Chỉ vài bước để chọn bàn, chọn giờ và đặt cọc giữ chỗ ngay trên website.',
                'content'  => "Từ nay bạn có thể đặt bàn trực tuyến chỉ trong vài bước: chọn ngày giờ, số khách, món muốn dùng trước và đặt cọc để giữ chỗ.\n\nKhoản đặt cọc sẽ được trừ vào hóa đơn khi bạn thanh toán tại nhà hàng. Nhân viên sẽ xác nhận ngay khi nhận được tiền cọc.",
            ],
            [
                'slug'     => 'thuc-don-mon-moi-mua-nay',
                'title'    => 'Thực đơn món mới mùa này',
                'category' => 'Món mới',
                'days_ago' => 5,
                'excerpt'  => 'Bếp trưởng giới thiệu loạt món mới, giữ trọn vị tươi của nguyên liệu theo mùa.',
                'content'  => "Mùa này bếp giới thiệu một loạt món mới, ưu tiên nguyên liệu theo mùa để giữ trọn vị tươi ngon.\n\nHãy vào mục Thực đơn để xem hình ảnh, giá và thêm món vào danh sách đặt trước.",
            ],
            [
                'slug'     => 'uu-dai-cho-khach-dat-ban-som',
                'title'    => 'Ưu đãi dành cho khách đặt bàn sớm',
                'category' => 'Khuyến mãi',
                'days_ago' => 7,
                'excerpt'  => 'Đặt bàn trước để nhận chỗ đẹp và những ưu đãi dành riêng cho khách đặt online.',
                'content'  => "Khách đặt bàn trực tuyến sẽ được ưu tiên giữ chỗ vào các khung giờ cao điểm cuối tuần.\n\nThông tin chi tiết về chương trình ưu đãi sẽ được cập nhật tại trang này, bạn nhớ theo dõi thường xuyên nhé.",
            ],
            [
                'slug'     => 'dem-nhac-acoustic-cuoi-tuan',
                'title'    => 'Đêm nhạc acoustic cuối tuần',
                'category' => 'Sự kiện',
                'days_ago' => 10,
                'excerpt'  => 'Thưởng thức bữa tối cùng những giai điệu acoustic nhẹ nhàng trong không gian sân vườn.',
                'content'  => "Cuối tuần này, nhà hàng tổ chức đêm nhạc acoustic nhẹ nhàng để bạn vừa thưởng thức bữa tối vừa thư giãn.\n\nĐể có vị trí đẹp, bạn nên đặt bàn trước qua website.",
            ],
            [
                'slug'     => 'bi-quyet-chon-mon-cho-nhom-ban-dong',
                'title'    => 'Bí quyết chọn món cho nhóm bạn đông người',
                'category' => 'Tin nhà hàng',
                'days_ago' => 14,
                'excerpt'  => 'Gợi ý cách kết hợp món khai vị, món chính và tráng miệng để cả nhóm đều hài lòng.',
                'content'  => "Đi đông người, bạn nên chọn món theo nguyên tắc: một vài món khai vị để chia sẻ, mỗi người một món chính và cả nhóm cùng chọn tráng miệng.\n\nNếu chưa biết chọn gì, nhân viên luôn sẵn sàng gợi ý theo khẩu vị của cả nhóm.",
            ],
            [
                'slug'     => 'thang-nay-co-gi-moi',
                'title'    => 'Tháng này có gì mới tại Aurora Garden?',
                'category' => 'Sự kiện',
                'days_ago' => 18,
                'excerpt'  => 'Điểm qua những hoạt động và cập nhật mới của nhà hàng trong tháng.',
                'content'  => "Tháng này nhà hàng cập nhật thêm nhiều hoạt động và cải tiến trong dịch vụ đặt bàn.\n\nHãy thường xuyên ghé trang Tin tức để không bỏ lỡ thông tin mới nhất.",
            ],
        ];

        foreach ($posts as $p) {
            $days = $p['days_ago'];
            unset($p['days_ago']);

            News::updateOrCreate(
                ['slug' => $p['slug']],
                $p + ['is_published' => true, 'published_at' => now()->subDays($days)]
            );
        }
    }
}
