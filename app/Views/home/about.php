<section class="page-hero">
    <p class="eyebrow">Về Yumio</p>
    <h1>Đồ ăn nhanh, ngon thật</h1>
    <p>Yumio ra đời từ một ý nghĩ đơn giản: ăn nhanh không có nghĩa là ăn qua loa.</p>
</section>

<section class="section section--tint">
    <div class="container">
        <p class="serif-lead">Chúng tôi chiên gà mới mỗi giờ, nướng bánh burger ngay khi bạn gọi và pha đồ uống từ trái cây thật. Nhanh, nhưng không bao giờ vội vàng.</p>
    </div>
</section>

<section class="section">
    <div class="container values">
        <div class="value-card" style="--tile-bg: var(--c-yellow)">
            <span class="display">01</span>
            <h3>Nấu mới mỗi ngày</h3>
            <p>Nguyên liệu nhập trong ngày, không dùng lại dầu chiên quá 8 giờ.</p>
        </div>
        <div class="value-card" style="--tile-bg: var(--c-peach)">
            <span class="display">02</span>
            <h3>Giao trong 30 phút</h3>
            <p>Đóng gói giữ nhiệt để món tới tay bạn vẫn giòn và nóng.</p>
        </div>
        <div class="value-card" style="--tile-bg: var(--c-green)">
            <span class="display">03</span>
            <h3>Giá minh bạch</h3>
            <p>Không phí ẩn. Miễn phí giao hàng cho đơn từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?>.</p>
        </div>
    </div>
</section>

<section class="section section--tint">
    <div class="container container--narrow prose">
        <h2 id="giao-hang">Chính sách giao hàng</h2>
        <p>Yumio giao hàng từ 7:00 đến 23:00 mỗi ngày trong bán kính 5km quanh cửa hàng. Phí giao hàng đồng giá <?= format_price(App\Models\Order::SHIPPING_FEE) ?>, miễn phí cho đơn từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?>.</p>
        <h2 id="dinh-duong">Dinh dưỡng &amp; dị ứng</h2>
        <p>Một số món có chứa gluten, trứng, sữa, đậu nành và mè. Nếu bạn bị dị ứng thực phẩm, hãy ghi chú khi đặt hàng hoặc liên hệ hotline 1900 6868 để được tư vấn.</p>
        <h2 id="dieu-khoan">Điều khoản sử dụng</h2>
        <p>Đây là website demo phục vụ đồ án môn Công nghệ Web. Đơn hàng không được giao thật và không phát sinh thanh toán.</p>
        <h2 id="bao-mat">Chính sách bảo mật</h2>
        <p>Mật khẩu được mã hoá bằng bcrypt. Thông tin cá nhân chỉ dùng để xử lý đơn hàng và không chia sẻ cho bên thứ ba.</p>
        <p class="muted">Hình ảnh sản phẩm được tham khảo từ website của KFC và McDonald's, chỉ dùng cho mục đích học tập.</p>
    </div>
</section>
