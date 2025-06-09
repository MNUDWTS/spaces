<!-- email-templates/event-invitation.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #EEF2FA !important;
            height: 800px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            border-radius: 12px;
        }

        .container {
            width: 600px;
            min-height: 400px;
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }

        .header {
            width: 100%;
            background-color: #262626 !important;
            padding: 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: white;
            font-size: 20px;
        }

        .header img {
            width: 100px;
            height: 33px;
        }

        .content {
            width: 100%;
            padding: 20px;
            background-color: #FBFBFB !important;
            text-align: left;
            border-radius: 0 0 12px 12px;
            color: black;
        }

        .content p,
        .content ul {
            font-size: 16px;
        }

        .booking-details>*+* {
            margin-top: 0.5rem;
        }

        .slots-wrap {
            display: flex;
            justify-content: start;
            align-items: center;
            flex-wrap: wrap;
        }

        .slot-item {
            width: 100px;
            font-size: 0.875rem;
            margin: 2px;
            padding: 2px 1px;
            border-radius: 0.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f1f5f9;
            border: 1px solid #d1d5db;
        }


        /* Медиазапрос для мобильных устройств */
        @media (max-width: 600px) {
            .container {
                width: 300px;
            }
        }

        /* Защита от темной темы */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #EEF2FA !important;
            }

            .header {
                background-color: #262626 !important;
            }

            .content {
                background-color: #FBFBFB !important;
                color: #000000 !important;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Spaces</h1>
            <img src="https://spaces.mnu.kz/wp-content/uploads/2024/12/logo_ff.png" alt="MNU Logo">
        </div>
        <div class="content" style="background-color:#FBFBFB;color:#000000">
            <h1><?php echo __('Hello', 'spaces_mnu_plugin') . ', ' . esc_html($name); ?>!</h1>
            <p>
                <?= sprintf(
                    __('We inform you that you have been invited to the event «%s».', 'spaces_mnu_plugin'),
                    '<span style="font-weight: 700;">' . $event_name . '</span>'
                ); ?>
            </p>
            <ul class="booking-details" style="list-style: none; padding: 0;">
                <li><?= __('Location', 'spaces_mnu_plugin') . ': ' . esc_html($event_location); ?></li>
                <li><?= __('Date', 'spaces_mnu_plugin') . ': ' . esc_html($date); ?></li>
                <li><?= __('Start Time', 'spaces_mnu_plugin') . ': ' . esc_html($event_public_start_time); ?></li>
            </ul>
            <div style="width:100%; border-top: solid 1px; border-color: #EBEBEB;padding-top: 10px; margin-top:10px;">
                <p style="font-size: 12px;font-weight:300; color:#666666">
                    <?= sprintf(
                        __('If you have any questions regarding your invitation, please, contact %s by email at %s.', 'spaces_mnu_plugin'),
                        '<span style="font-weight: 700;">' . $event_responsible_name  . '</span>',
                        '<span style="font-weight: 700;">' . $event_responsible_email  . '</span>'
                    ); ?>
                </p>
            </div>
        </div>
    </div>

</body>

</html>