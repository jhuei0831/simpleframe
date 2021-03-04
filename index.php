<?php
    $root = "./";
    include('_config/settings.php');

    use _models\database as DB;
    use _models\message as MG;
    use _models\security as SC;
    
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $token = SC::defend_filter($_POST['token']);
        unset($_POST['token']);
        // 將陣列的value轉成數字來執行驗證
        $data = array_map('intval', SC::defend_filter($_POST));
        // 防止input的value被竄改
        if (max($data) > 5 || min($data) < 1) {
            MG::show_message('請重新填寫，謝謝。');
            MG::redirect(WEB_ADDRESS);
        }
        else{
            $data['token'] = $token;
            $insert = DB::table('audio_quality')->insert($data);
            if ($insert) {
                MG::show_message('成功送出，謝謝。');
                MG::redirect(WEB_ADDRESS);
            }
            else{
                MG::show_message('送出失敗，請再嘗試一次，謝謝。');
                MG::redirect(WEB_ADDRESS);
            }
        }  
    }

    $quality = [
        '1' => '無法辨識',
        '2' => '依稀辨識',
        '3' => '尚可',
        '4' => '清楚',
        '5' => '非常清楚'
    ]
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>音檔試聽</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
</head>
<body style="background-color: grey; font-family: Microsoft JhengHei;">
    <div class="container mt-4">
        <div class="jumbotron" style="background-color:antiquewhite">
            <div class="container">
                <h1 class="display-5"><b>音檔試聽</b></h1>
                <p class="lead"><b>請聽完音檔後根據<font color="red">錄音品質</font>選出對應的評論</b></p>
            </div>
        </div>
        <form method="post">
            <input type="hidden" name="token" value="<?=TOKEN?>">
            <div class="card">
                <div class="card-body">
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>音檔</th>
                                <th>根據音檔錄音品質進行評論</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b><font color="red">原始音檔</font></b></td>
                                <td><audio src="<?=WEB_SRC?>audio/female/4.wav" controls controlsList="nodownload"></td>
                                <td></td>
                            </tr>
                            <? for($i=1; $i<=3; $i++): ?>
                                <tr>
                                    <td></td>
                                    <td><audio src="<?=WEB_SRC?>audio/female/<?=$i?>.mp3" controls controlsList="nodownload"></audio></td>
                                    <td>
                                        <? for($j=1;$j<=5;$j++): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="female<?=$i?>" id="female<?=$i.$j?>" value="<?=$j?>" required>
                                                <label class="form-check-label" for="female<?=$i.$j?>"><?=$quality[$j]?></label>
                                            </div>
                                        <? endfor ?>
                                    </td>
                                </tr>
                            <? endfor ?>
                                <tr>
                                    <td></td>
                                    <td><audio src="<?=WEB_SRC?>audio/female/4.wav" controls controlsList="nodownload"></audio></td>
                                    <td>
                                        <? for($j=1;$j<=5;$j++): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="female4" id="female4<?=$j?>" value="<?=$j?>" required>
                                                <label class="form-check-label" for="female4<?=$j?>"><?=$quality[$j]?></label>
                                            </div>
                                        <? endfor ?>
                                    </td>
                                </tr> 
                            <tr>
                                <td><b><font color="red">原始音檔</font></b></td>
                                <td><audio src="<?=WEB_SRC?>audio/male/4.wav" controls controlsList="nodownload"></audio></td>
                                <td></td>
                            </tr> 
                                <? for($i=1; $i<=3; $i++): ?>
                                <tr>
                                    <td></td>
                                    <td><audio src="<?=WEB_SRC?>audio/male/<?=$i?>.mp3" controls controlsList="nodownload"></audio></td>
                                    <td>
                                        <? for($j=1;$j<=5;$j++): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="male<?=$i?>" id="male<?=$i.$j?>" value="<?=$j?>" required>
                                                <label class="form-check-label" for="male<?=$i.$j?>"><?=$quality[$j]?></label>
                                            </div>
                                        <? endfor ?>
                                    </td>
                                </tr>
                            <? endfor ?>
                                <tr>
                                    <td></td>
                                    <td><audio src="<?=WEB_SRC?>audio/male/4.wav" controls controlsList="nodownload"></audio></td>
                                    <td>
                                        <? for($j=1;$j<=5;$j++): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="male4" id="male<?=$j?>" value="<?=$j?>" required>
                                                <label class="form-check-label" for="male<?=$j?>"><?=$quality[$j]?></label>
                                            </div>
                                        <? endfor ?>
                                    </td>
                                </tr>     
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <input type="submit" class="btn btn-success" value="送出結果">
                </div>
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>