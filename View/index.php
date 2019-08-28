<?php

use Likemusic\AutomatedUpdatePlayersGames\Contracts\AdminPage\DateType;
/** @var string $type */
/** @var string $dateStart */
/** @var string $dateEnd */
/** @var string $dateMin */
/** @var string $dateMax */
?>
<script>
    jQuery(function ($) {
        $('input[name=type]').change(function () {
            var value = this.value;

            var $dateStart = $('#div-date-from');
            var $dateEnd = $('#div-date-to');

            if ($.inArray(value, ['current', 'yesterday']) !== -1) {
                $dateStart.hide();
            } else {
                $dateStart.show();
            }

            if ($.inArray(value, ['current', 'yesterday', 'day']) !== -1) {
                $dateEnd.hide();
            } else {
                $dateEnd.show();
            }

            $dateStartLabel = $dateStart.find('label');

            if (value === 'day') {
                $dateStartLabel.text('выбранный день');
            } else if(value === 'period') {
                $dateStartLabel.text('начальная дата');
            }
        });
    });

</script>
<div class="wrap">
    <h2><?= get_admin_page_title() ?></h2>
    <br/>

    <?php if ($messages): ?>
        <div class="save-changes-success notice notice-success">
            <?php
                foreach ($messages as $message) {
                    echo "<p>{$message}</p>";
                }
            ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessages): ?>
        <div class="save-changes-success notice notice-error">
            <?php
            foreach ($errorMessages as $errorMessage) {
                echo "<p>{$errorMessage}</p>";
            }
            ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">

        <div class="type-selector">
            <?php $checkedAttribute = ' checked'; ?>
            <label><input type="radio" name="type" value="<?= DateType::CURRENT ?>" <?php if ($type == 'current'): echo $checkedAttribute; endif; ?> />На текущий момент</label><br />
            <label><input type="radio" name="type" value="<?= DateType::YESTERDAY ?>" <?php if ($type == 'yesterday'): echo $checkedAttribute; endif; ?>/>За вчерашний день</label><br />
            <label><input type="radio" name="type" value="<?= DateType::DAY ?>" <?php if ($type == 'day'): echo $checkedAttribute; endif; ?>/>За заданный день</label><br />
            <label><input type="radio" name="type" value="<?= DateType::PERIOD ?>" <?php if ($type == 'period'): echo $checkedAttribute; endif; ?>/>За заданный период</label><br />
        </div>

        <br/>

        <div class="dates">
            <?php $hideStyleAttribute = 'style="display: none;"'; ?>
            <div id="div-date-from" <?php if (in_array($type, [DateType::CURRENT, DateType::YESTERDAY])) {echo $hideStyleAttribute;} ?>>
                <input type="date" name="date-start" id="date-start" value="<?= $dateStart ?>" min="<?= $dateMin?>" max="<?= $dateMax ?>" > <label for="date-start">начальная дата</label>
            </div>

            <div id="div-date-to" <?php if (in_array($type, [DateType::CURRENT, DateType::YESTERDAY, DateType::DAY])) {echo $hideStyleAttribute;} ?>>
                <input type="date" name="date-end" id="date-end" value="<?= $dateEnd ?>"  min="<?= $dateMin?>" max="<?= $dateMax ?>"> <label for="date-end">конечная дата</label>
            </div>
        </div>

        <br/>

        <div>
            <input type="submit" class="button-primary" value="Обновить"/>
        </div>
    </form>
</div>
