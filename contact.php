<?php
session_start();

$message = "";
$messageType = "";

/* ضع بريدك الإلكتروني هنا */
$receiverEmail = "info@example.com";

/* إنشاء رمز حماية للنموذج */
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function cleanInput($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $csrfToken = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $csrfToken)) {
        $message = "حدث خطأ في التحقق من الطلب. أعد تحميل الصفحة وحاول مرة أخرى.";
        $messageType = "error";
    } else {

        $name = cleanInput($_POST["name"] ?? "");
        $email = cleanInput($_POST["email"] ?? "");
        $phone = cleanInput($_POST["phone"] ?? "");
        $subject = cleanInput($_POST["subject"] ?? "");
        $userMessage = cleanInput($_POST["message"] ?? "");

        if (
            empty($name) ||
            empty($email) ||
            empty($subject) ||
            empty($userMessage)
        ) {
            $message = "يرجى تعبئة جميع الحقول المطلوبة.";
            $messageType = "error";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "يرجى إدخال بريد إلكتروني صحيح.";
            $messageType = "error";

        } elseif (strlen($name) < 3) {
            $message = "يجب أن يحتوي الاسم على ثلاثة أحرف على الأقل.";
            $messageType = "error";

        } elseif (strlen($userMessage) < 10) {
            $message = "يجب ألا تقل الرسالة عن عشرة أحرف.";
            $messageType = "error";

        } else {

            $subjectNames = [
                "general"   => "استفسار عام",
                "service"   => "طلب خدمة",
                "complaint" => "شكوى أو ملاحظة",
                "technical" => "دعم فني"
            ];

            $selectedSubject = $subjectNames[$subject] ?? "رسالة جديدة";

            $emailSubject = "رسالة جديدة من الموقع: " . $selectedSubject;

            $emailBody  = "تم استلام رسالة جديدة من نموذج التواصل\n\n";
            $emailBody .= "الاسم: " . $name . "\n";
            $emailBody .= "البريد الإلكتروني: " . $email . "\n";
            $emailBody .= "رقم الهاتف: " . ($phone ?: "غير متوفر") . "\n";
            $emailBody .= "الموضوع: " . $selectedSubject . "\n\n";
            $emailBody .= "الرسالة:\n" . $userMessage . "\n";

            $headers  = "From: Website <no-reply@yourdomain.com>\r\n";
            $headers .= "Reply-To: " . $email . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($receiverEmail, $emailSubject, $emailBody, $headers)) {
                $message = "تم إرسال رسالتك بنجاح، وسنتواصل معك قريبًا.";
                $messageType = "success";

                $_POST = [];
                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            } else {
                $message = "تعذر إرسال الرسالة حاليًا. تأكد من إعدادات البريد في السيرفر.";
                $messageType = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>تواصل معنا</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Tajawal", Arial, sans-serif;
            background: #f4f7fa;
            color: #1f2937;
            min-height: 100vh;
        }

        header {
            background: #0f4c5c;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            color: white;
            font-size: 25px;
            font-weight: 700;
            text-decoration: none;
        }

        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        nav a:hover,
        nav a.active {
            color: #7dd3c7;
        }

        .contact-section {
            width: 90%;
            max-width: 1100px;
            margin: 60px auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h1 {
            color: #0f4c5c;
            font-size: 36px;
            margin-bottom: 10px;
        }

        .section-title p {
            color: #6b7280;
            line-height: 1.8;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 30px;
        }

        .contact-info,
        .contact-form {
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .contact-info {
            background: linear-gradient(145deg, #0f4c5c, #147d82);
            color: white;
        }

        .contact-form {
            background: white;
        }

        .contact-info h2,
        .contact-form h2 {
            font-size: 25px;
            margin-bottom: 25px;
        }

        .info-item {
            margin-bottom: 25px;
        }

        .info-item h3 {
            font-size: 17px;
            margin-bottom: 7px;
            color: #b8fff4;
        }

        .info-item p,
        .info-item a {
            color: white;
            text-decoration: none;
            line-height: 1.7;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            font-family: inherit;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #147d82;
            box-shadow: 0 0 0 3px rgba(20, 125, 130, 0.12);
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 9px;
            background: #147d82;
            color: white;
            font-family: inherit;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0f4c5c;
            transform: translateY(-2px);
        }

        .alert {
            margin-bottom: 20px;
            padding: 14px;
            border-radius: 9px;
            text-align: center;
            line-height: 1.7;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        footer {
            margin-top: 60px;
            padding: 20px;
            background: #0f4c5c;
            color: white;
            text-align: center;
        }

        @media (max-width: 800px) {
            header {
                justify-content: center;
                gap: 15px;
            }

            nav {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            .contact-container {
                grid-template-columns: 1fr;
            }

            .section-title h1 {
                font-size: 29px;
            }

            .contact-info,
            .contact-form {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

<header>
    <a href="index.php" class="logo">
        اسم الموقع
    </a>

    <nav>
        <a href="index.php">الرئيسية</a>
        <a href="about.php">من نحن</a>
        <a href="services.php">الخدمات</a>
        <a href="contact.php" class="active">تواصل معنا</a>
    </nav>
</header>

<main class="contact-section">

    <div class="section-title">
        <h1>تواصل معنا</h1>

        <p>
            نسعد باستقبال استفساراتكم وملاحظاتكم، وسنرد عليكم
            في أقرب وقت ممكن.
        </p>
    </div>

    <div class="contact-container">

        <section class="contact-info">
            <h2>معلومات التواصل</h2>

            <div class="info-item">
                <h3>العنوان</h3>
                <p>المملكة العربية السعودية</p>
            </div>

            <div class="info-item">
                <h3>رقم الهاتف</h3>

                <a href="tel:+966500000000">
                    +966 50 000 0000
                </a>
            </div>

            <div class="info-item">
                <h3>البريد الإلكتروني</h3>

                <a href="mailto:info@example.com">
                    info@example.com
                </a>
            </div>

            <div class="info-item">
                <h3>أوقات العمل</h3>

                <p>
                    الأحد إلى الخميس:
                    8:00 صباحًا – 4:00 مساءً
                </p>
            </div>
        </section>

        <form
            class="contact-form"
            action="contact.php"
            method="POST"
        >
            <h2>أرسل رسالتك</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?= $messageType; ?>">
                    <?= $message; ?>
                </div>
            <?php endif; ?>

            <input
                type="hidden"
                name="csrf_token"
                value="<?= $_SESSION["csrf_token"]; ?>"
            >

            <div class="form-group">
                <label for="name">الاسم الكامل</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    placeholder="اكتب اسمك الكامل"
                    value="<?= cleanInput($_POST["name"] ?? ""); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="150"
                    placeholder="example@email.com"
                    value="<?= cleanInput($_POST["email"] ?? ""); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="phone">رقم الهاتف</label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    maxlength="20"
                    placeholder="05xxxxxxxx"
                    value="<?= cleanInput($_POST["phone"] ?? ""); ?>"
                >
            </div>

            <div class="form-group">
                <label for="subject">موضوع الرسالة</label>

                <select id="subject" name="subject" required>
                    <option value="">اختر موضوع الرسالة</option>

                    <option
                        value="general"
                        <?= ($_POST["subject"] ?? "") === "general"
                            ? "selected"
                            : ""; ?>
                    >
                        استفسار عام
                    </option>

                    <option
                        value="service"
                        <?= ($_POST["subject"] ?? "") === "service"
                            ? "selected"
                            : ""; ?>
                    >
                        طلب خدمة
                    </option>

                    <option
                        value="complaint"
                        <?= ($_POST["subject"] ?? "") === "complaint"
                            ? "selected"
                            : ""; ?>
                    >
                        شكوى أو ملاحظة
                    </option>

                    <option
                        value="technical"
                        <?= ($_POST["subject"] ?? "") === "technical"
                            ? "selected"
                            : ""; ?>
                    >
                        دعم فني
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="userMessage">الرسالة</label>

                <textarea
                    id="userMessage"
                    name="message"
                    maxlength="2000"
                    placeholder="اكتب رسالتك هنا..."
                    required
                ><?= cleanInput($_POST["message"] ?? ""); ?></textarea>
            </div>

            <button type="submit">
                إرسال الرسالة
            </button>
        </form>

    </div>
</main>

<footer>
    <p>
        جميع الحقوق محفوظة &copy;
        <?= date("Y"); ?>
        اسم الموقع
    </p>
</footer>

</body>
</html>