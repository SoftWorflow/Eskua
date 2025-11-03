#!/bin/bash

# Ir al directorio principal del proyecto
cd "$(dirname "$0")"

# Ejecutar el comando de Tailwind
npx @tailwindcss/cli -i ./apps/front_php/src/input.css -o ./apps/front_php/src/output.css --watch
