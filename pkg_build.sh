#!/bin/bash
set -euo pipefail

VERSION=${1:-"0.2.0"}
PKG_NAME="storcli-temp-package-${VERSION}"
OUTDIR="${PWD}"
OUTFILE="${OUTDIR}/${PKG_NAME}.txz"

TMPDIR=$(mktemp -d)
mkdir -p "${TMPDIR}/install"

cp -R source/* "${TMPDIR}/"

find "${TMPDIR}" -name ".DS_Store" -type f -delete || true

cat > "${TMPDIR}/install/slack-desc" << 'EOF'
storcli-temp: StorCLI ROC Temperature Dashboard Tile
storcli-temp:
storcli-temp: Displays ROC temperature for one or more storcli controllers
storcli-temp: as a Dashboard custom tile.
storcli-temp: Requires storcli installed (typically /usr/local/bin/storcli).
storcli-temp:
EOF

cd "${TMPDIR}"
if command -v makepkg >/dev/null 2>&1; then
  makepkg -l y -c y "${OUTFILE}"
else
  tar -cJf "${OUTFILE}" .
fi
cd - >/dev/null

rm -rf "${TMPDIR}"

echo "Built: ${OUTFILE}"
if command -v sha256sum >/dev/null 2>&1; then
  sha256sum "${OUTFILE}" | awk '{print "SHA256: "$1}'
else
  shasum -a 256 "${OUTFILE}" | awk '{print "SHA256: "$1}'
fi
