Add-Type -AssemblyName System.Windows.Forms
$form = New-Object System.Windows.Forms.Form
$form.TopMost = $true
$f = New-Object System.Windows.Forms.FolderBrowserDialog
$f.Description = "Select Backup Storage Folder - Paolo Paolo Management"
$f.ShowNewFolderButton = $true
if ($args.Count -gt 0 -and $args[0] -and (Test-Path $args[0])) {
    $f.SelectedPath = $args[0]
}
$result = $f.ShowDialog($form)
if ($result -eq [System.Windows.Forms.DialogResult]::OK) {
    [Console]::OutputEncoding = [System.Text.Encoding]::UTF8
    Write-Output $f.SelectedPath
}
$form.Dispose()
