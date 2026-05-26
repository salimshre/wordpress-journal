# Docker Desktop WSL/Virtualization Fix

This note records the Docker Desktop startup problem and the recovery steps that fixed it on this machine.

## Problem

Docker Desktop failed to start with a message like:

```text
Virtualization support wasn't detected.
Contact your IT admin to enable virtualization or check system requirements.
```

After virtualization was enabled in BIOS, Docker still failed. After repairing Windows virtualization components and restarting, Docker moved to this message:

```text
WSL needs updating.
Your version of Windows Subsystem for Linux (WSL) is too old.
Run: wsl --update
```

## Machine Context

- OS: Windows 10 Pro
- Build: 19045
- Device: Acer Aspire E5-571
- Docker Desktop: 4.74.0
- Docker engine after fix: 29.4.3
- WSL kernel after fix: 6.6.114.1-microsoft-standard-WSL2

## What Was Already Done

BIOS virtualization was enabled manually before the software repair. Windows confirmed that the CPU/firmware side was valid:

```powershell
systeminfo.exe
```

The important lines were:

```text
Hyper-V Requirements:
  VM Monitor Mode Extensions: Yes
  Virtualization Enabled In Firmware: Yes
  Second Level Address Translation: Yes
  Data Execution Prevention Available: Yes
```

That meant the issue was not the BIOS setting anymore.

## Diagnostics Used

Check Hyper-V requirements:

```powershell
Get-ComputerInfo -Property HyperVRequirement*,OsName,OsVersion,WindowsProductName,WindowsVersion
```

Check Windows boot hypervisor settings:

```powershell
bcdedit /enum "{current}"
```

Check WSL status:

```powershell
wsl --status
```

Check Windows virtualization features:

```powershell
dism.exe /online /get-featureinfo /featurename:Microsoft-Hyper-V-All
dism.exe /online /get-featureinfo /featurename:VirtualMachinePlatform
dism.exe /online /get-featureinfo /featurename:Microsoft-Windows-Subsystem-Linux
dism.exe /online /get-featureinfo /featurename:HypervisorPlatform
```

Check virtualization services:

```powershell
Get-Service vmcompute,LxssManager,com.docker.service -ErrorAction SilentlyContinue |
  Select-Object Name,Status,StartType
```

Check whether `vmcompute` exists:

```powershell
Test-Path C:\Windows\System32\vmcompute.exe
sc.exe queryex vmcompute
```

In the broken state, `vmcompute.exe` was missing from `System32`, and `sc.exe queryex vmcompute` returned:

```text
The specified service does not exist as an installed service.
```

That pointed to a broken Windows Hyper-V/WSL component registration.

## Repair Steps

Run these from an elevated PowerShell prompt.

Repair Windows component store:

```powershell
DISM.exe /Online /Cleanup-Image /RestoreHealth
```

Repair protected Windows system files:

```powershell
sfc.exe /scannow
```

Restart Windows after the repair.

After restart, confirm `vmcompute` exists and is running:

```powershell
sc.exe queryex vmcompute
```

Expected result:

```text
SERVICE_NAME: vmcompute
STATE              : 4  RUNNING
```

Then update WSL:

```powershell
wsl --update
```

The successful output was:

```text
Installing: Windows Subsystem for Linux
Windows Subsystem for Linux has been installed.
```

Shut WSL down cleanly:

```powershell
wsl --shutdown
```

Start Docker Desktop:

```powershell
Start-Process -FilePath "C:\Program Files\Docker\Docker\Docker Desktop.exe"
```

Check Docker's WSL distro:

```powershell
wsl --list --verbose
```

Expected result:

```text
NAME             STATE    VERSION
docker-desktop   Running  2
```

Verify Docker engine:

```powershell
docker info
```

Expected result includes:

```text
Server Version: 29.4.3
Operating System: Docker Desktop
OSType: linux
Kernel Version: 6.6.114.1-microsoft-standard-WSL2
Name: docker-desktop
```

## Final State

The fix was successful when:

- `vmcompute` was installed and running.
- `wsl --status` showed default version `2`.
- `wsl --list --verbose` showed `docker-desktop` running on version `2`.
- `docker info` returned both client and server information.

## Notes

If Docker says virtualization is missing even after enabling it in BIOS, check Windows Hyper-V/WSL services before changing BIOS again. On this machine, BIOS virtualization was already correct; Windows needed component repair and WSL needed updating.
