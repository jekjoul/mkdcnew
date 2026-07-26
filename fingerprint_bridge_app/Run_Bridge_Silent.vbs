Set WshShell = CreateObject("WScript.Shell")
WshShell.Run chr(34) & WshShell.CurrentDirectory & "\Start_Bridge.bat" & chr(34), 0
Set WshShell = Nothing
