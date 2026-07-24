Set WshShell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")
currentDir = fso.GetAbsolutePathName(".")

' Jalankan Start_Bridge.bat secara tersembunyi
WshShell.Run chr(34) & currentDir & "\Start_Bridge.bat" & chr(34), 0, False
