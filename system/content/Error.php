<p class="error_title">Произошла ошибка</p>
<div class="error_box">
    <div class="trace">
        <p>Стек вызовов</p>
        <ul>
            <?php foreach ($trace as $ln): ?>
                <li>
                    <span style="color: #de0000"><?= ($ln['file'] ?? "[internal function]") ?></span>:
                    <span style="color: #0e0eff"><?= ($ln['line'] ?? "-") ?></span> =>
                    <span style="color: #c1c100"><?= $ln['function'] ?>()</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div style="width: 50%;">
        <h1><?= $message ?></h1>
        <p>Место возникновения: <strong><?= $file ?></strong> : <strong><?= $line ?></strong></p>
    </div>
</div>
