dim sPath, sSpid, sElpid, sTspid

set oFileSystem = WScript.CreateObject("Scripting.FileSystemObject")

' Path to the directory with the application
sPath = oFileSystem.GetAbsolutePathName(".")

' Pid files
sSpid  = sPath & "/bin/.spid"
sTspid = sPath & "/bin/.tspid"


' Stop Server
if oFileSystem.FileExists(sSpid) then
    iErrorReturn = StopProc(sSpid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Server: ", iErrorReturn
        WScript.Quit
    end if
end if
' Stop Task scheduler
if oFileSystem.FileExists(sTspid) then
    iErrorReturn = StopProc(sTspid)
    if iErrorReturn <> 0 then
        Wscript.echo "Could not stop Task scheduler: ", iErrorReturn
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
