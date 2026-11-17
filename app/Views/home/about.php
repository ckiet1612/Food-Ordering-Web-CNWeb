<section class="story-hero">
    <div class="story-hero__copy" data-reveal><p class="eyebrow">✳ Chuyện của Yumio</p><h1>ĂN NHANH.<br><em>Vui thật lâu.</em></h1><p>Một miếng giòn. Một ngụm mát. Một bữa ăn làm ngày thường bỗng vui hơn. Đó là điều Yumio muốn đặt vào mỗi phần ăn.</p><a class="btn btn--solid btn--lg" href="#chuyen-bep">Kéo xuống, kể bạn nghe <?= icon('right', 22) ?></a></div>
    <div class="story-art" data-parallax="24"><div class="story-art__paper" aria-hidden="true"></div><span class="story-art__scribble" aria-hidden="true">Ngon là<br>một cảm giác!</span><img src="<?= asset('images/yumio-burger.svg') ?>" alt="Burger Yumio vui nhộn, minh hoạ bằng SVG" width="640" height="560"><span class="yumio-sticker story-art__stamp">MADE OF<br><b>GOOD MOOD</b></span></div>
    <span class="story-hero__foot">TỪ MỘT CHIẾC BỤNG ĐÓI → ĐẾN MỘT BỮA VUI</span>
</section>

<div class="story-ribbon" aria-hidden="true"><span>GIÒN TAN ✳ ĐẬM VỊ ✳ VUI HẾT CỠ ✳ YUMIO ✳ GIÒN TAN ✳ ĐẬM VỊ ✳ VUI HẾT CỠ ✳</span></div>

<section class="story-chapter" id="chuyen-bep">
    <div class="story-chapter__visual story-chapter__visual--red" data-reveal><span class="story-chapter__number" aria-hidden="true">01</span><img src="<?= asset('images/foods/ga-ran-truyen-thong.webp') ?>" alt="Gà rán giòn của Yumio" loading="lazy" width="500" height="500"><span class="story-chapter__note">Rôm rốp một miếng!</span></div>
    <div class="story-chapter__copy" data-reveal><p class="eyebrow">Bắt đầu từ vị ngon</p><h2>MỘT CHÚT GIÒN.<br><em>Cả ngày vui.</em></h2><p>Yumio tin một bữa ăn ngon không cần phức tạp. Đôi khi, chỉ là phần gà rán bạn đang thèm, ăn cùng người bạn thích và chẳng phải nghĩ quá nhiều.</p><p>Gà rán, burger, món ăn kèm hay một chút ngọt cuối bữa — mỗi lựa chọn là một cách tự thưởng cho mình.</p><a class="link-underline" href="<?= url('/menu') ?>">Tìm món hợp tâm trạng <?= icon('right', 22) ?></a></div>
</section>

<section class="story-chapter story-chapter--reverse">
    <div class="story-chapter__visual story-chapter__visual--yellow" data-reveal><span class="story-chapter__number" aria-hidden="true">02</span><img src="<?= asset('images/foods/burger-ga-gion.webp') ?>" alt="Burger gà giòn" loading="lazy" width="500" height="500"><span class="story-chapter__note">Bữa này, theo ý bạn.</span></div>
    <div class="story-chapter__copy" data-reveal><p class="eyebrow">Bữa ăn của riêng bạn</p><h2>THÈM GÌ?<br><em>Cứ chọn đi.</em></h2><p>Một mình gọi burger, cả hội chia combo. Thêm khoai, bớt một phần, hay để lại lời nhắn cho bếp: bạn quyết định bữa ăn hôm nay.</p><p>Giỏ hàng luôn hiển thị rõ số lượng, giá món và phí giao. Vui là được chọn; yên tâm là biết mình đang trả bao nhiêu.</p><a class="btn" href="<?= url('/menu/combo') ?>">Rủ hội gọi combo <?= icon('plus', 22) ?></a></div>
</section>

<section class="story-delivery" data-reveal><div><p class="eyebrow">Chặng cuối của một bữa ngon</p><h2>TỪ BẾP<br><em>tới cửa nhà.</em></h2><p>Chọn món. Gửi địa chỉ. Xem lại rồi xác nhận. Bạn theo dõi từng bước chuẩn bị và giao hàng ngay trong đơn của mình.</p><a href="<?= url(auth() ? '/orders' : '/login') ?>" class="btn btn--light btn--lg">Theo dõi đơn <?= icon('truck', 24) ?></a></div><ol><li><span>01</span><div><h3>Chọn món bạn thèm</h3><p>Thêm, bớt và xem tổng tiền trong giỏ.</p></div></li><li><span>02</span><div><h3>Đúng món, đúng địa chỉ</h3><p>Tìm gợi ý địa chỉ hoặc nhập tay. Luôn xem lại trước khi đặt.</p></div></li><li><span>03</span><div><h3>Nhận món, thanh toán COD</h3><p>Phí giao <?= format_price(App\Models\Order::SHIPPING_FEE) ?>. Miễn phí từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?>.</p></div></li></ol></section>

<section class="story-policies container container--narrow"><p class="eyebrow">Một chút thông tin rõ ràng</p><h2>Ăn vui. Biết rõ.</h2>
    <details id="giao-hang"><summary>Giao hàng &amp; thanh toán <?= icon('plus', 24) ?></summary><p>Phí giao cố định <?= format_price(App\Models\Order::SHIPPING_FEE) ?>, miễn phí từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?>. Khách nhập địa chỉ hoặc chọn gợi ý, admin phân công tài xế. Khoảng cách bản đồ là đường chim bay để tham khảo, chưa phải quãng đường giao. Đơn mới thanh toán COD.</p></details>
    <details id="dinh-duong"><summary>Dinh dưỡng &amp; dị ứng <?= icon('plus', 24) ?></summary><p>Một số món có chứa gluten, trứng, sữa, đậu nành và mè. Hãy ghi rõ nhu cầu hoặc dị ứng trong ghi chú đặt hàng. Thông tin minh hoạ trong đồ án không thay thế việc xác nhận nguyên liệu của cửa hàng thực tế.</p></details>
    <details id="dieu-khoan"><summary>Về bản demo Yumio <?= icon('plus', 24) ?></summary><p>Yumio là đồ án Công nghệ Web. Đơn được lưu thật trong database nhưng không phát sinh giao hàng hoặc thanh toán ngoài đời. Ảnh món từ bộ tài nguyên nhóm đã chuẩn bị, chỉ dùng học tập; minh hoạ burger SVG được vẽ riêng cho Yumio.</p></details>
    <details id="bao-mat"><summary>Thông tin của bạn <?= icon('plus', 24) ?></summary><p>Mật khẩu được băm. Khi gõ địa chỉ, nội dung tìm kiếm được gửi qua server tới Photon/OpenStreetMap để lấy gợi ý. Có thể tắt gợi ý bằng cấu hình GEOCODING_ENABLED=0 và nhập tay. Bản đồ tải nền từ OpenStreetMap; định vị thiết bị chỉ được xin khi bạn chủ động chọn.</p></details>
</section>

<section class="story-finale" data-reveal><span aria-hidden="true">✷</span><p>ĐỌC TỚI ĐÂY, ĐÓI CHƯA?</p><h2>THÈM THÌ<br><em>YUMIO!</em></h2><a class="btn btn--light btn--lg" href="<?= url('/menu') ?>">Đặt một bữa vui <?= icon('right', 24) ?></a></section>
