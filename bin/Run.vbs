dim sAddr, sPort, sPath, sServer, sConsole, sEventListener, sTaskScheduler, sSpid, sElpid

set oFileSystem = WScript.CreateObject("Scripting.FileSystemObject")

' IP-address of the server with application
sAddr = "0.0.0.0"
' Port on the application server
sPort = "56780"
' Path to the directory with the application
sPath = oFileSystem.GetAbsolutePathName("..")
' Path to php.exe
sPhp = "php"

' Pid files
sSpid  = sPath & "/bin/.spid"
sTspid = sPath & "/bin/.tspid"

' Commands to run server and Task Scheduler
sServer        = chr(34) & sPhp & chr(34) & " -S " & sAddr & ":" & sPort & " -t " & chr(34) & sPath & "/web" & chr(34) & " " & chr(34) & sPath & "/app/router.php" & chr(34) & " >nul 2>&1"
sTaskScheduler = chr(34) & sPhp & chr(34) & " -f " & chr(34) & sPath & "/app/console" & chr(34) & " animedb:task-scheduler"

' Stop Server if running
if oFileSystem.FileExists(sSpid) then
    iErrorReturn = StopProc(sSpid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Server: ", iErrorReturn
        WScript.Quit
    end if
end if
' Stop Task scheduler if running
if oFileSystem.FileExists(sTspid) then
    iErrorReturn = StopProc(sTspid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Task scheduler: ", iErrorReturn
        WScript.Quit
    end if
end if


' Run Server
iErrorReturn = StartProc(sServer, sSpid)
if iErrorReturn <> 0 then
    Wscript.echo "Could not start Server: ", iErrorReturn
    WScript.Quit
end if
' Run Task scheduler
iErrorReturn = StartProc(sTaskScheduler, sTspid)
if iErrorReturn <> 0 then
    Wscript.echo "Could not start Task scheduler: ", iErrorReturn
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

' Stop process
function StopProc(sPidFile)
    ' get pid from file
    set oFileSystem = WScript.CreateObject("Scripting.FileSystemObject")
    set oTextStream = oFileSystem.OpenTextFile(sPidFile, 1, false)
    iProcessID = oTextStream.ReadAll()
    oTextStream.Close
    oFileSystem.GetFile(sPidFile).Delete
    ' stop process
    dim sQry
    sQry = "SELECT * FROM Win32_Process WHERE ProcessID = '" & iProcessID & "'"
    set oWMISrvc = GetObject("WinMgmts:")
    for each item in oWMISrvc.ExecQuery(sQry)
        item.Terminate
    next
end function
