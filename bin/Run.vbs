dim sAddr, sPort, sPath, sServer, sConsole, sTaskManager, sCron, sSpid, sTmpid

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
sCpid  = sPath & "/bin/.cpid"

sConsole = sPath & "/bin/php/php.exe -f " & sPath & "/app/console "

' Commands to run server, task manager and cron
sServer      = sPath & "/bin/php/php.exe -S " & sAddr & ":" & sPort & " -t " & sPath & "/web " & sPath & "/app/router.php > nul 2> nul"
sTaskManager = sConsole & "animedb:task-manager > nul 2> nul"
sCron        = sConsole & "animedb:cron > nul 2> nul"

' Stop Server if running
if oFileSystem.FileExists(sSpid) then
    iErrorReturn = StopProc(sSpid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Server: ", iErrorReturn
        WScript.Quit
    end if
end if
' Stop Task manager if running
if oFileSystem.FileExists(sTmpid) then
    iErrorReturn = StopProc(sTmpid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Task manager: ", iErrorReturn
        WScript.Quit
    end if
end if
' Stop Cron if running
if oFileSystem.FileExists(sCpid) then
    iErrorReturn = StopProc(sCpid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Cron: ", iErrorReturn
        WScript.Quit
    end if
end if


' Run Server
iErrorReturn = StartProc(sServer, sSpid)
if iErrorReturn <> 0 then
    Wscript.echo "Could not start Server: ", iErrorReturn
    WScript.Quit
end if
' Run Task manager
iErrorReturn = StartProc(sTaskManager, sTmpid)
if iErrorReturn <> 0 then
    Wscript.echo "Could not start Task manager: ", iErrorReturn
    WScript.Quit
end if
' Run Cron
iErrorReturn = StartProc(sCron, sCpid)
if iErrorReturn <> 0 then
    Wscript.echo "Could not start Cron: ", iErrorReturn
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
