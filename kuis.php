<!DOCTYPE html>
<html>
  <title>Lingkaran Kelas 11</title>

<head>
    <title>Soal Pilihan Ganda</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .navbar {
            width: 100%;
            background-color: #0056b3;
            padding: 15px;
            color: white;
            text-align: center;
        }
        .main-content {
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 1200px;
        }
        .container {
            width: 70%;
            max-width: 700px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .sidebar {
            width: 20%;
            margin-left: 20px;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 90px;
        }
        .sidebar h4 {
            color: #0056b3;
            font-size: 18px;
            margin-bottom: 10px;
            text-align: center;
        }
        .question-number {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .question-number a {
            margin: 5px;
            padding: 10px;
            display: inline-block;
            width: 40px;
            text-align: center;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .not-answered {
            background-color: #6c757d;
        }
        .answered {
            background-color: #007bff;
        }
        .soal {
            margin-bottom: 20px;
        }
        h3 {
            color: #0056b3;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        label {
            font-size: 16px;
            cursor: pointer;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        button[name="previous"] {
            background-color: #6c757d;
            color: white;
        }
        button[name="next"] {
            background-color: #007bff;
            color: white;
        }
        button[name="submit"] {
            background-color: #28a745;
            color: white;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<?php
session_start();

// Fungsi untuk membaca soal dari file
function bacaSoalDariFile($filePath) {
    $soalPilihanGanda = [];
    $file = fopen($filePath, "r");

    while (($line = fgets($file)) !== false) {
        $data = explode('|', trim($line));
        $soalPilihanGanda[] = [
            "pertanyaan" => $data[0],
            "pilihan" => array_slice($data, 1, 5),
            "jawabanBenar" => $data[6]
        ];
    }
    fclose($file);
    return $soalPilihanGanda;
}

// Fungsi untuk menampilkan kelompok soal
function tampilkanKelompokSoal($soalPilihanGanda, $startIndex, $jumlahSoalPerHalaman) {
    for ($i = $startIndex; $i < $startIndex + $jumlahSoalPerHalaman && $i < count($soalPilihanGanda); $i++) {
        echo "<div class='soal' id='soal-$i'><h3>Soal " . ($i + 1) . "</h3>";
        echo "<p>{$soalPilihanGanda[$i]['pertanyaan']}</p>";

        $pilihanJawaban = $soalPilihanGanda[$i]['pilihan'];
        shuffle($pilihanJawaban);

        echo "<ul>";
        foreach ($pilihanJawaban as $pilihan) {
            $checked = (isset($_SESSION['jawaban'][$i]) && $_SESSION['jawaban'][$i] == $pilihan) ? 'checked' : '';
            echo "<li><label><input type='radio' name='jawaban{$i}' value='$pilihan' $checked> $pilihan</label></li>";
        }
        echo "</ul></div>";
    }
}

// Memuat soal dari file
$soalPilihanGanda = bacaSoalDariFile("soal.txt");

// Jumlah soal per halaman
$jumlahSoalPerHalaman = 1;

// Menyimpan jawaban pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    for ($i = 0; $i < $jumlahSoalPerHalaman; $i++) {
        $index = $_SESSION['halaman'] * $jumlahSoalPerHalaman + $i;
        if (isset($_POST["jawaban{$index}"])) {
            $_SESSION['jawaban'][$index] = $_POST["jawaban{$index}"];
        }
    }

    // Navigasi halaman
    if (isset($_POST['next'])) {
        $_SESSION['halaman'] = ($_SESSION['halaman'] ?? 0) + 1;
    } elseif (isset($_POST['previous'])) {
        $_SESSION['halaman'] = max(0, ($_SESSION['halaman'] ?? 0) - 1);
    }
} elseif (isset($_GET['halaman'])) {
    $_SESSION['halaman'] = max(0, intval($_GET['halaman']));
} else {
    $_SESSION['halaman'] = $_SESSION['halaman'] ?? 0;
}

// Jika tombol Submit ditekan
if (isset($_POST['submit'])) {
    $totalBenar = 0;
    foreach ($soalPilihanGanda as $index => $soal) {
        if (isset($_SESSION['jawaban'][$index]) && $_SESSION['jawaban'][$index] == $soal['jawabanBenar']) {
            $totalBenar++;
        }
    }
    $jumlahSoal = count($soalPilihanGanda);
    $nilai = ($totalBenar / $jumlahSoal) * 100;

    echo "<h2>Hasil Anda:</h2>";
    echo "<p>Jawaban benar: $totalBenar dari $jumlahSoal soal.</p>";
    echo "<p>Nilai Anda: $nilai%</p>";

    // Tombol kembali ke home
    echo "<div style='text-align: center; margin-top: 20px;'>
            <a href='home.html' style='text-decoration: none;'>
                <button type='button' style='background-color: #0056b3; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: 0.3s ease;'>
                    Kembali ke Home
                </button>
            </a>
          </div>";
    session_destroy();
    exit;
}

$startIndex = $_SESSION['halaman'] * $jumlahSoalPerHalaman;
?>   
<div class="main-content">
    <div class="container">
        <form method="post">
            <?php tampilkanKelompokSoal($soalPilihanGanda, $startIndex, $jumlahSoalPerHalaman); ?>

            <div class="buttons">
                <?php if ($startIndex > 0): ?>
                    <button type="submit" name="previous">Previous</button>
                <?php endif; ?>

                <?php if ($startIndex + $jumlahSoalPerHalaman < count($soalPilihanGanda)): ?>
                    <button type="submit" name="next">Next</button>
                <?php else: ?>
                    <button type="submit" name="submit">Submit</button>
                <?php endif; ?>
            </div>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="home.html" style="text-decoration: none;">
                <button type="button" style="background-color: #0056b3; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: 0.3s ease;">
                    Kembali ke Home
                </button>
            </a>
        </div>
    </div>

    <div class="sidebar">
        <h4>Navigasi Soal</h4>
        <div class="question-number">
            <?php foreach ($soalPilihanGanda as $index => $soal): ?>
                <?php 
                    $halaman = floor($index / $jumlahSoalPerHalaman); 
                    $isAnswered = isset($_SESSION['jawaban'][$index]);
                    $class = $isAnswered ? 'answered' : 'not-answered';
                ?>
                <a href="?halaman=<?php echo $halaman; ?>" 
                   class="<?php echo $class; ?>" 
                   style="<?php echo ($_SESSION['halaman'] === $halaman) ? 'border: 2px solid #0056b3;' : ''; ?>">
                    <?php echo $index + 1; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>
