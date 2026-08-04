Set WshShell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

strPath = fso.GetParentFolderName(WScript.ScriptFullName)
exeFile = strPath & "\MKDC_Scanner_Bridge.exe"
psFile  = strPath & "\scanner_gui.ps1"

If fso.FileExists(exeFile) Then
    WshShell.Run """" & exeFile & """", 0, False
ElseIf fso.FileExists(psFile) Then
    WshShell.Run "powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File """ & psFile & """", 0, False
Else
    MsgBox "File executable atau script Scanner Bridge tidak ditemukan!", 16, "Error MKDC Scanner Bridge"
End If
