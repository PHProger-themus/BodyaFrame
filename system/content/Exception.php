<div style="margin: 50px; width: 600px">
    <h1 style="font-size: 35px; text-transform: uppercase">Исключение</h1>
    <p style="font-size: 20px">Было поймано исключение:</p>
    <div>
        <table style="width: 100%; font-size: 18px;" cellspacing="0">
            <tr style="background: #dadada">
                <td style="padding: 5px">Код:</td>
                <td style="padding: 5px"><?= $code ?></td>
            </tr>
            <tr style="padding: 5px;">
                <td style="padding: 5px">Сообщение:</td>
                <td style="padding: 5px"><?= $message ?></td>
            </tr>
            <tr style="background: #dadada">
                <td style="padding: 5px">Файл:</td>
                <td style="padding: 5px"><?= $file ?></td>
            </tr>
            <tr style="padding: 5px;">
                <td style="padding: 5px">Строка:</td>
                <td style="padding: 5px"><?= $line ?></td>
            </tr>
        </table>
    </div>
</div>