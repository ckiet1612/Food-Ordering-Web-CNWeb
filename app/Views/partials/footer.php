<footer class="site-footer" id="lien-he">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="<?= url('/') ?>" class="footer-logo">YUMIO</a>
            <p class="serif">Đồ ăn nhanh, ngon thật.<br>Nấu mới mỗi ngày, giao nóng tận tay.</p>
        </div>

        <div class="footer-cols">
            <details class="footer-col" data-footer-col>
                <summary>Liên hệ</summary>
                <ul>
                    <li><a href="tel:19006868">Hotline: 1900 6868</a></li>
                    <li><a href="mailto:hello@yumio.vn">hello@yumio.vn</a></li>
                    <li>1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội</li>
                </ul>
            </details>
            <details class="footer-col" data-footer-col>
                <summary>Về Yumio</summary>
                <ul>
                    <li><a href="<?= url('/about') ?>">Câu chuyện của chúng tôi</a></li>
                    <li><a href="<?= url('/stores') ?>">Hệ thống cửa hàng</a></li>
                    <li><a href="<?= url('/menu') ?>">Thực đơn</a></li>
                </ul>
            </details>
            <details class="footer-col" data-footer-col>
                <summary>Hỗ trợ</summary>
                <ul>
                    <li><a href="<?= url('/orders') ?>">Theo dõi đơn hàng</a></li>
                    <li><a href="<?= url('/about') ?>#giao-hang">Chính sách giao hàng</a></li>
                    <li><a href="<?= url('/about') ?>#dinh-duong">Dinh dưỡng &amp; dị ứng</a></li>
                </ul>
            </details>
            <details class="footer-col" data-footer-col>
                <summary>Pháp lý</summary>
                <ul>
                    <li><a href="<?= url('/about') ?>#dieu-khoan">Điều khoản sử dụng</a></li>
                    <li><a href="<?= url('/about') ?>#bao-mat">Chính sách bảo mật</a></li>
                </ul>
            </details>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Yumio. Đồ án môn Công nghệ Web.</p>
        <p class="footer-note">Hình ảnh sản phẩm chỉ dùng cho mục đích học tập.</p>
    </div>
</footer>
