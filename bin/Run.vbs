dim sAddr, sPort, sPath, sServer, sTaskManager, sSpid, sTmpid

set oFileSystem = WScript.CreateObject("Scripting.FileSystemObject")

' IP-address of the server with application
sAddr = "0.0.0.0"
' Port on the application server
sPort = "56780"
' Path to the directory with the application
sPath = oFileSystem.GetAbsolutePathName("..")

' Pid files
sSpid  = sPath & "/bin/.spid"
sTmpid = sPath & "/bin/.tmpid"

' Commands to run server and task manager
sServer = sPath & "/bin/php/php.exe -S " & sAddr & ":" & sPort & " -t " & sPath & "/web " & sPath & "/app/router.php > nul 2> nul"
sTaskManager = sPath & "/bin/php/php.exe -f " & sPath & "/app/task_manager.php > nul 2> nul"

' Server is run?
if oFileSystem.FileExists(sSpid) then
    Wscript.echo "Server is already running"
    WScript.Quit
end if
' Task manager is run?
if oFileSystem.FileExists(sTmpid) then
    Wscript.echo "Task manager is already running"
    WScript.Quit
end if

' run server
iErrorReturn = StartProc(sServer, sSpid)
if iErrorReturn <> 0 then
    Wscript.echo "Could not start Server: ", iErrorReturn
	WScript.Quit
end if

' run task manager
iErrorReturn = StartProc(sTaskManager, sTmpid)
if iErrorReturn <> 0 then
    Wscript.echo "Could not start Task manager: ", iErrorReturn
	WScript.Quit
end if

Wscript.Echo "Application successfully launched"


' Start process
function StartProc(sProgramToRun, sPidFile)
    ' run process
    Set oConfig = GetObject("WinMgmts:").get("Win32_ProcessStartup").SpawnInstance_
    oConfig.ShowWindow = 0
    StartProc = GetObject("WinMgmts:Win32_Process").Create(sProgramToRun, null, oConfig, iProcessID)
	' put in to file pid
    set oTextStream = WScript.CreateObject("Scripting.FileSystemObject").CreateTextFile(sPidFile)
    oTextStream.Write(iProcessID)
    oTextStream.Close
end function
