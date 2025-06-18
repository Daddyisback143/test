<?php
// Test PDF generation
require_once __DIR__ . '/vendor/autoload.php';

try {
    // Create temp directory if it doesn't exist
    $tempDir = __DIR__ . '/vendor/mpdf/mpdf/tmp';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    
    $mpdf = new \Mpdf\Mpdf([
        'tempDir' => $tempDir,
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 15,
        'margin_bottom' => 15
    ]);
    
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #0077cc; border-radius: 10px; background: #f9f9f9;'>
        <h2 style='color: #0077cc; text-align: center; margin-bottom: 20px;'>Test PDF Generation</h2>
        <div style='background: white; padding: 20px; border-radius: 8px;'>
            <p><strong>Test:</strong> PDF generation is working!</p>
            <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
            <hr style='margin: 20px 0;'>
            <p style='text-align: center; color: #666;'><em>PDF generation test successful!</em></p>
        </div>
    </div>";
    
    $mpdf->WriteHTML($html);
    
    // Output PDF to browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="test.pdf"');
    echo $mpdf->Output('', 'S');
    
} catch (Exception $e) {
    echo "PDF Generation Error: " . $e->getMessage();
}
?>



