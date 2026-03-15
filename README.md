# storcli-temp (Unraid Plugin)

Dashboard custom tile for displaying **StorCLI ROC temperature** (supports multiple controllers).

This repo follows the recommended packaging pattern from plugin-docs:

- `source/` contains files that will be installed under `/usr/local/emhttp/plugins/storcli-temp/`
- `pkg_build.sh` builds a `.txz` Slackware package
- `storcli-temp.plg` downloads the `.txz` from GitHub Releases and installs it via `upgradepkg`

## Requirements

- Unraid `>= 6.11.9`
- `storcli` installed at `/usr/local/bin/storcli`

## Build (on Unraid or any system with `makepkg`)

```bash
./pkg_build.sh 0.2.0
```

This generates:

- `storcli-temp-package-0.2.0.txz`
- a `SHA256:` line you must paste into `storcli-temp.plg` (`packageSHA256`)

## Release

1. Create a GitHub Release tag: `v0.2.0`
2. Upload `storcli-temp-package-0.2.0.txz` as a release asset
3. Update `storcli-temp.plg`:

- `version` entity
- `packageSHA256` entity

## Install (users)

In Unraid WebUI:

- Plugins
- Install Plugin
- Paste:

`https://raw.githubusercontent.com/ecarry/unraid-storcli-temp-plugin/main/storcli-temp.plg`

## Enable the Dashboard tile

- Dashboard
- Content Manager
- Enable: `Show StorCLI ROC temperature`
