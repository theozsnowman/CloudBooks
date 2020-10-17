/*  
    CloudBooks. Open source hotel and restaurant management software.
    Copyright (C) 2020 Vittorio Lo Mele

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published
    by the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
    Contact me at: vittorio[at]mrbackslash.it
*/

/* globals Chart:false, feather:false */

$(document).ready(function () {
  feather.replace(); //replaces icons
  $('#logtbl').DataTable({
    "order": [[ 0, "desc" ]],
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Italian.json"
    }
  });
});
