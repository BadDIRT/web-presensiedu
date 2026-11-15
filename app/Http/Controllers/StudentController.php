<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        return view('admin.students');
    }

    /**
     * 🆕 Download kartu absensi sebagai PNG (1.97" x 3.34" @ 300 DPI).
     */
    public function downloadCardPng(Student $student): StreamedResponse
    {
        $token = $this->generateToken($student);

        // 1. Generate QR sebagai PNG
        $qrResult = Builder::create()
            ->data($token)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(400)
            ->margin(0)
            ->foregroundColor(new Color(10, 15, 36))
            ->backgroundColor(new Color(255, 255, 255))
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        $qrImage = imagecreatefromstring($qrResult->getString());

        // 2. Canvas PNG (1.97" x 3.34" @ 300 DPI = 591 x 1002 px)
        $dpi = 300;
        $canvasW = (int) round(1.97 * $dpi);  // 591
        $canvasH = (int) round(3.34 * $dpi);  // 1002

        $canvas = imagecreatetruecolor($canvasW, $canvasH);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // 3. Palette warna
        $navy   = imagecolorallocate($canvas, 10, 15, 36);
        $navy2  = imagecolorallocate($canvas, 19, 26, 54);
        $gold   = imagecolorallocate($canvas, 255, 204, 51);
        $goldDk = imagecolorallocate($canvas, 26, 19, 6);
        $red    = imagecolorallocate($canvas, 255, 71, 87);
        $blue   = imagecolorallocate($canvas, 56, 189, 248);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        $gray   = imagecolorallocate($canvas, 148, 163, 184);
        $green  = imagecolorallocate($canvas, 34, 224, 123);
        $slate  = imagecolorallocate($canvas, 203, 213, 225);

        // 4. Background navy solid (gradient simulated dengan 2 band)
        imagefill($canvas, 0, 0, $navy);
        // Band atas lebih terang (efek radial glow)
        $bandH = 200;
        for ($y = 0; $y < $bandH; $y++) {
            $ratio = $y / $bandH;
            $r = (int) (10 + (19 - 10) * $ratio);
            $g = (int) (15 + (26 - 15) * $ratio);
            $b = (int) (36 + (54 - 36) * $ratio);
            imageline($canvas, 0, $y, $canvasW, $y, imagecolorallocate($canvas, $r, $g, $b));
        }

        // 5. Border emas tipis di pinggir kartu
        $inset = 12;
        for ($i = 0; $i < 2; $i++) {
            imagerectangle(
                $canvas,
                $inset + $i,
                $inset + $i,
                $canvasW - $inset - $i,
                $canvasH - $inset - $i,
                $gold
            );
        }

        // 6. Font
        $fontBold = $this->findFont(true);
        $fontReg  = $this->findFont(false);

        // 7. Banner "QR CODE PRIBADI" (gold background, dark text)
        $bannerText = 'QR CODE PRIBADI';
        $bannerFontSize = 13;
        $bannerPadX = 18;
        $bannerPadY = 8;
        $bannerY = 40;

        // Hitung lebar text
        $bbox = imagettfbbox($bannerFontSize, 0, $fontBold, $bannerText);
        $textW = $bbox[2] - $bbox[0];
        $textH = $bbox[1] - $bbox[7];
        $bannerW = $textW + ($bannerPadX * 2);
        $bannerH = $textH + ($bannerPadY * 2);
        $bannerX = (int) (($canvasW - $bannerW) / 2);

        // Banner background gold
        imagefilledrectangle(
            $canvas,
            $bannerX,
            $bannerY,
            $bannerX + $bannerW,
            $bannerY + $bannerH,
            $gold
        );
        // Banner text dark
        $textX = $bannerX + $bannerPadX;
        $textY = $bannerY + $bannerPadY + $textH;
        imagettftext($canvas, $bannerFontSize, 0, $textX, $textY, $goldDk, $fontBold, $bannerText);

        // 8. Deskripsi text
        $desc1 = 'Tunjukkan QR ini ke admin untuk di-scan.';
        $desc2 = 'QR ini permanen - simpan screenshot untuk cadangan.';
        $this->drawTextCentered($canvas, $desc1, $canvasW / 2, $bannerY + $bannerH + 25, 9, $slate, $fontReg);
        $this->drawTextCentered($canvas, $desc2, $canvasW / 2, $bannerY + $bannerH + 42, 9, $slate, $fontReg);

        // 9. Info PESERTA
        $pesertaY = $bannerY + $bannerH + 75;
        $this->drawTextCentered($canvas, 'PESERTA', $canvasW / 2, $pesertaY, 8, $gray, $fontReg);

        $name = strtoupper($student->name);
        if (strlen($name) > 22) {
            $name = substr($name, 0, 20) . '..';
        }
        $this->drawTextCentered($canvas, $name, $canvasW / 2, $pesertaY + 22, 15, $white, $fontBold);

        $meta = $student->id_siswa . '  -  ' . $student->kelas;
        $this->drawTextCentered($canvas, $meta, $canvasW / 2, $pesertaY + 42, 11, $gold, $fontBold);

        // 10. QR dengan border gradient (gold -> red -> blue)
        $qrSize = 380;
        $qrX = (int) (($canvasW - $qrSize) / 2);
        $qrY = $pesertaY + 75;

        // Border gradient tebal (8px) dengan multi-stop color
        $borderW = 10;
        $stops = 40; // jumlah step gradient
        for ($i = 0; $i < $stops; $i++) {
            $ratio = $i / $stops;
            // Interpolasi: gold (255,204,51) -> red (255,71,87) -> blue (56,189,248)
            if ($ratio < 0.5) {
                $t = $ratio * 2;
                $r = (int) (255 + (255 - 255) * $t);
                $g = (int) (204 + (71 - 204) * $t);
                $b = (int) (51 + (87 - 51) * $t);
            } else {
                $t = ($ratio - 0.5) * 2;
                $r = (int) (255 + (56 - 255) * $t);
                $g = (int) (71 + (189 - 71) * $t);
                $b = (int) (87 + (248 - 87) * $t);
            }
            $c = imagecolorallocate($canvas, $r, $g, $b);

            // Outer rectangle (per offset)
            $offset = (int) ($borderW * ($i / $stops));
            imagerectangle(
                $canvas,
                $qrX - $borderW + $offset,
                $qrY - $borderW + $offset,
                $qrX + $qrSize + $borderW - $offset,
                $qrY + $qrSize + $borderW - $offset,
                $c
            );
        }

        // QR white background
        imagefilledrectangle(
            $canvas,
            $qrX,
            $qrY,
            $qrX + $qrSize,
            $qrY + $qrSize,
            $white
        );
        // QR code itself
        imagecopyresampled($canvas, $qrImage, $qrX, $qrY, 0, 0, $qrSize, $qrSize, 400, 400);

        // Label "PRESENSIEDU" di bawah QR
        $presensiLabel = 'PRESENSIEDU';
        $this->drawTextCentered($canvas, $presensiLabel, $canvasW / 2, $qrY + $qrSize + 25, 9, $gray, $fontBold);

        // 11. Badge "STATUS PERMANEN" (green border + checkmark)
        $badgeY = $qrY + $qrSize + 60;
        $badgeText = 'STATUS PERMANEN';
        $badgeFontSize = 10;
        $badgePadX = 15;
        $badgePadY = 6;

        // Calculate badge width with checkmark
        $checkmarkText = 'V ';
        $badgeFullText = $checkmarkText . $badgeText;
        $bbox = imagettfbbox($badgeFontSize, 0, $fontBold, $badgeFullText);
        $textW = $bbox[2] - $bbox[0];
        $textH = $bbox[1] - $bbox[7];
        $badgeW = $textW + ($badgePadX * 2);
        $badgeH = $textH + ($badgePadY * 2);
        $badgeX = (int) (($canvasW - $badgeW) / 2);

        // Badge background (transparent-ish green)
        $bgGreen = imagecolorallocatealpha($canvas, 34, 224, 123, 100);
        imagefilledrectangle(
            $canvas,
            $badgeX,
            $badgeY,
            $badgeX + $badgeW,
            $badgeY + $badgeH,
            $bgGreen
        );
        // Badge border green
        imagerectangle(
            $canvas,
            $badgeX,
            $badgeY,
            $badgeX + $badgeW,
            $badgeY + $badgeH,
            $green
        );
        // Badge text
        $badgeTextX = $badgeX + $badgePadX;
        $badgeTextY = $badgeY + $badgePadY + $textH;
        imagettftext($canvas, $badgeFontSize, 0, $badgeTextX, $badgeTextY, $green, $fontBold, $badgeFullText);

        // 12. Footer tips
        $tip1 = 'QR ini adalah QR pribadi Anda dan tidak akan berubah.';
        $tip2 = 'Boleh di-screenshot untuk cadangan.';
        $tip3 = 'Pastikan layar cukup terang saat di-scan admin.';

        $footerY = $canvasH - 80;
        $this->drawTextCentered($canvas, $tip1, $canvasW / 2, $footerY, 8, $slate, $fontReg);
        $this->drawTextCentered($canvas, $tip2, $canvasW / 2, $footerY + 15, 8, $slate, $fontReg);
        $this->drawTextCentered($canvas, $tip3, $canvasW / 2, $footerY + 30, 8, $slate, $fontReg);

        // 13. Output PNG
        return response()->stream(function () use ($canvas, $qrImage) {
            imagepng($canvas, null, 9);
            imagedestroy($canvas);
            imagedestroy($qrImage);
        }, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="kartu-absensi-' . $student->id_siswa . '.png"',
        ]);
    }

    /**
     * Cari file font TTF di sistem (Windows / Linux).
     */
    private function findFont(bool $bold): ?string
    {
        $candidates = $bold
            ? [
                'C:\\Windows\\Fonts\\arialbd.ttf',       // Windows Arial Bold
                'C:\\Windows\\Fonts\\segoeui.ttf',       // Windows Segoe UI
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',  // Linux
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            ]
            : [
                'C:\\Windows\\Fonts\\arial.ttf',
                'C:\\Windows\\Fonts\\segoeui.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }
        return null; // fallback ke font built-in GD
    }

    /**
     * Render teks tengah (auto-center horizontal).
     */
    private function drawTextCentered(
        $img,
        string $text,
        float $centerX,
        float $y,
        float $size,
        $color,
        ?string $fontFile
    ): void {
        if ($fontFile) {
            // imagettftext: size in points, $y is baseline
            $bbox = imagettfbbox($size, 0, $fontFile, $text);
            $textWidth = $bbox[2] - $bbox[0];
            $x = $centerX - ($textWidth / 2);
            imagettftext($img, $size, 0, (int) $x, (int) $y, $color, $fontFile, $text);
        } else {
            // Fallback: font built-in GD (jelek tapi works)
            $fontNum = $size > 12 ? 5 : ($size > 9 ? 4 : 3);
            $charWidth = imagefontwidth($fontNum);
            $textWidth = strlen($text) * $charWidth;
            $x = $centerX - ($textWidth / 2);
            imagestring($img, $fontNum, (int) $x, (int) ($y - $size - 2), $text, $color);
        }
    }

    /**
     * Token QR permanen (SAMA PERSIS dengan yang ada di QrDisplay.php).
     */
    private function generateToken(Student $student): string
    {
        $payload = [
            'sid'  => $student->id,
            'id'   => $student->id_siswa,
            'name' => $student->name,
            'sig'  => hash_hmac('sha256', $student->id . '|' . $student->id_siswa, config('app.key')),
        ];

        return base64_encode(json_encode($payload));
    }
}
