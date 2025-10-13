<?php

namespace App\Support;

class SimplePdf
{
    protected function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    public function render(array $lines): string
    {
        $content = "";
        $y = 800; // start near top of A4
        foreach ($lines as $line) {
            $escaped = $this->escape($line);
            $content .= "BT /F1 12 Tf 50 {$y} Td ({$escaped}) Tj ET\n";
            $y -= 16;
            if ($y < 50) { // prevent overflow minimal
                break;
            }
        }

        $stream = $content;
        $len = strlen($stream);

        $objects = [];
        $objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n";
        $objects[] = "2 0 obj<< /Type /Pages /Count 1 /Kids [3 0 R] >>endobj\n";
        $objects[] = "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>endobj\n";
        $objects[] = "4 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n";
        $objects[] = "5 0 obj<< /Length {$len} >>stream\n{$stream}endstream endobj\n";
        $objects[] = "6 0 obj<< /Producer (SimplePdf) >>endobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }

        $xrefStart = strlen($pdf);
        $count = count($objects) + 1; // include object 0
        $pdf .= "xref\n0 {$count}\n";
        $pdf .= sprintf("%010d %05d f\n", 0, 65535);
        for ($i = 1; $i < $count; $i++) {
            $pdf .= sprintf("%010d %05d n\n", $offsets[$i], 0);
        }
        $pdf .= "trailer<< /Size {$count} /Root 1 0 R /Info 6 0 R >>\n";
        $pdf .= "startxref\n{$xrefStart}\n%%EOF";

        return $pdf;
    }
}