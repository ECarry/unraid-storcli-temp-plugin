# storcli-temp (Unraid Plugin)

Dashboard custom tile for displaying LSI/Avago/Broadcom RAID controller **ROC temperature** via `storcli`.

## Requirements

- Unraid `>= 6.11.9`
- `storcli` available at:
  - `/usr/local/bin/storcli`

## What it shows

- ROC temperature in °C
- Basic status (Support Temperature / ROC Sensor)

## Install

### Install from GitHub (recommended for users)

1. Edit `storcli-temp.plg` and replace:

- `YOUR_GITHUB_USER/YOUR_REPO`
- `REPLACE_WITH_SHA256` (for both files)

2. Publish the repo.

3. In Unraid WebUI:

- Plugins
- Install Plugin
- Paste this URL:

`https://raw.githubusercontent.com/YOUR_GITHUB_USER/YOUR_REPO/main/storcli-temp.plg`

### Install locally (for development)

Copy files to your flash:

- `/boot/config/plugins/storcli-temp.plg`
- `/boot/config/plugins/storcli-temp/include/temperature.php`
- `/boot/config/plugins/storcli-temp/storcli-temp-dashboard.page`

Then install:

```bash
/usr/local/sbin/plugin install /boot/config/plugins/storcli-temp.plg
```

## Enable the Dashboard tile

- Open Dashboard
- Click **Content Manager**
- Enable: `Show StorCLI ROC temperature (c0)`

## Development notes

- Runtime files live under:

`/usr/local/emhttp/plugins/storcli-temp/`

- Flash cached files live under:

`/boot/config/plugins/storcli-temp/`

## Release workflow (simple)

Compute SHA256 for the two source files and paste into `storcli-temp.plg`:

```bash
sha256sum src/temperature.php
sha256sum src/storcli-temp-dashboard.page
```

Then bump `version` in `storcli-temp.plg`.
