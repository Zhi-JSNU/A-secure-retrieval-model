<!--Author: Zhi Create date:2021/7/4-->
<?php
//     echo 'The current directory is :'.getcwd();
//     echo '<br>';

//     $filename="/var/www/CPABE/key";
//     if(!is_dir($filename))
//     {
//         echo "This is not a directory";
//        mkdir($filename);
//     }
//     echo 'The current directory is :'.getcwd();

  //  chdir(key);
  //  echo "$filename.<br>";
    echo 'Change the directory to:'.getcwd();

    system('cpabe-setup');

    echo '<br>';

    system('ls');

    echo '<br>';

    $publickey="/var/www/CPABE/key/pub_key";

    $masterkey="/var/www/CPABE/key/master_key";

    if(file_exists($publickey))
    {
        if (file_exists($masterkey))

        {
            echo "Key generation finished!";
        }
        else
            echo "Key generation failed!";
    }
else
       echo "Key generation failed!";

echo "<script >alert('Key generation finished!');history.back();</script>";


?>

