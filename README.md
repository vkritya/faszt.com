# Faszt.com
A dick joke taken too far.

This is a modified version of [librespeed/speedtest](https://github.com/librespeed/speedtest).
## Significant changes
I take the officially built librespeed container and replace the frontend in it with a custom one made with Vue and anime.js. \
I didn't replace the favicon yet, I probably should.
## Installation/build
- Clone the repo, then either
  - Run the docker compose file
  - Build the image and run it

There's a build arg, `VERSION`, if you want to build a specific version instead of latest, use it.

## TODO (probably won't do)
- Maybe use a store instead of shitload of refs in App.vue
- Better dev experience (hmr for example)
- Test in different modes, not just standalone
- Reimplement broken modes (they are probably broken)
- Add backend server selection
- Check compatibility with upstream 6.0 (new design system)


## Licensing
Modifications - Copyright (C) 2025-2026 Kristóf Veres \
Original - Copyright (C) 2016-2024 Federico Dossena

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/lgpl>.
