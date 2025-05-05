<?php
// This script fixes the syntax error in notes/index.blade.php

// Path to the file
$filePath = __DIR__ . '/resources/views/notes/index.blade.php';

// Check if file exists
if (!file_exists($filePath)) {
    echo "File not found: $filePath\n";
    exit(1);
}

// Read the file content
$fileContent = file_get_contents($filePath);

// Look for the issue - sometimes there's a stray character at the end of the function
$pattern = '/function prepareAddNoteModal\(event\) \{(.+?)return true;\s*\}\s*<\/script>\s*@endsection/s';
$replacement = 'function prepareAddNoteModal(event) {$1return true;
}
</script>
@endsection';

// Make the replacement
$fixedContent = preg_replace($pattern, $replacement, $fileContent);

// Check if replacement was successful
if ($fixedContent === $fileContent) {
    echo "No changes made, pattern not found.\n";
    
    // Try an alternative approach - simply ensure the last part is correct
    $lastPart = '</script>
@endsection';
    
    // Make sure the file ends with the correct syntax
    if (substr($fileContent, -strlen($lastPart)) !== $lastPart) {
        echo "Fixing file ending...\n";
        
        // Remove any trailing content after the last proper closing of script tag
        $lastScriptPos = strrpos($fileContent, '</script>');
        
        if ($lastScriptPos !== false) {
            $fixedContent = substr($fileContent, 0, $lastScriptPos + 9) . "\n@endsection";
        }
    }
}

// Save the fixed content back to the file
if ($fixedContent !== $fileContent) {
    file_put_contents($filePath, $fixedContent);
    echo "File fixed successfully!\n";
} else {
    echo "File content was not modified.\n";
}

echo "Done.\n";
?> 