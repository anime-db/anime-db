dim sPath, sSpid, sTmpid, sCpid

set oFileSystem = WScript.CreateObject("Scripting.FileSystemObject")

' Path to the directory with the application
sPath = oFileSystem.GetAbsolutePathName("..")

' Pid files
sSpid  = sPath & "/bin/.spid"
sTmpid = sPath & "/bin/.tmpid"
sCpid  = sPath & "/bin/.cpid"


' Stop Server
if oFileSystem.FileExists(sSpid) then
    iErrorReturn = StopProc(sSpid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Server: ", iErrorReturn
        WScript.Quit
    end if
end if
' Stop Task manager
if oFileSystem.FileExists(sTmpid) then
    iErrorReturn = StopProc(sTmpid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Task manager: ", iErrorReturn
        WScript.Quit
    end if
end if
' Stop Cron
if oFileSystem.FileExists(sCpid) then
    iErrorReturn = StopProc(sCpid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Cron: ", iErrorReturn
        WScript.Quit
    end if
end if

Wscript.Echo "Application successfully stopped"


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
