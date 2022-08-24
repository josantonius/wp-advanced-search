/**
 * This file is part of https://github.com/josantonius/wp-advanced-search repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
document.addEventListener('DOMContentLoaded', function ()
{
  Vue.component('gmap-autocomplete', VueGoogleMaps.Autocomplete)
  Vue.component('gmap-map', VueGoogleMaps.Map)
  Vue.component('VueGoogleMapsPlacesAggregator', VueGoogleMapsPlacesAggregator.VueGoogleMapsPlacesAggregator)
  Vue.component('VueGoogleAutocomplete', VueGoogleAutocomplete.VueGoogleAutocomplete)

  Vue.use(VueGoogleMaps, {
    load: {
      key: geolocationAttendanceControlPlaces.google_api_key,
      libraries: 'places'
    },
  })
  new Vue({
    el: '#app',
    data: vm => ({
      selected: [],
      search: '',
      place: null,
      centre: '',
      activity: '',
      headerTitle: 'Centros',
      startTime: false,
      display: true,
      start_hour: null,
      endTime: false,
      end_hour: null,
      pagination: {
        descending: true,
        page: 1,
        rowsPerPage: 10,
        sortBy: 'id'
      },
      placeholder: 'Dirección',
      className: '',
      id: '',
      value: '',
      options: {
        componentRestrictions: {
          country: 'es'
        }
      },
      currentID: null,
      zoom: 10,
      center: {
        lat: 37.392529,
        lng: -5.994072
      },
      width: '100%',
      height: '400px',
      page: 'centres',
      maxHeight: '600px',
      maxWidth: '100%',
      markers: [],
      toolbarTitle: 'Asistencias',
      toolbarColor: 'indigo',
      formatDate: 'DD [de] MMMM [de] YYYY',
      formatHour: 'hh:mm',
      defaultDateOrder: 'DEC',
      defaultHourOrder: 'DEC',
      locale: 'es',
      dialog: false,
      dialogConfirm: false,
      dialogTitle: '',
      dialogSubtitle: '',
      headers: [
        {
          text: 'ID',
          align: 'left',
          value: 'id',
          sortable: true
        },
        {
          text: 'Centro',
          align: 'right',
          value: 'centre',
          sortable: true
        },
        { text: 'Actividad', value: 'activity', sortable: true, align: 'right' },
        { text: 'Inicio', value: 'start_hour', sortable: true, align: 'right' },
        { text: 'Fin', value: 'end_hour', sortable: true, align: 'right' },
        { text: 'Acciones', value: 'centre', sortable: false, align: 'center' }
      ],
      editedIndex: -1
    }),

    created()
    {
      this.selectCentres()
      setTimeout(() =>
      {
        this.selectCentres()
      }, 180000)

    },

    methods: {
      getTemplate(centre, activity, start_hour, end_hour, address)
      {
        return `
          <div id="content">
            <div class="gac-heading">
              <h1 class="gac-first-heading">${centre}</h1>
              <h3 class="gac-second-heading">${activity} - ${start_hour} - ${end_hour}</span></h3>
              
            </div>
            <div id="bodyContent">
              <p>${address}</p>
            </div>
          </div>
        `
      },

      request(type, action, params)
      {
        var data = new URLSearchParams()
        params = params || {}
        data.append('action', action)
        data.append('nonce', geolocationAttendanceControlPlaces.nonce)
        for (var key in params)
        {
          data.append(key, params[key])
          console.log(key, params[key])
        }
        return new Promise((resolve, reject) =>
        {
          return axios({
            method: type,
            url: geolocationAttendanceControlPlaces.ajax_url,
            data: data
          })
            .then(response =>
            {
              resolve(response.data)
            })
            .catch(error =>
            {
              console.log(error)
              reject(error)
            })
        })
      },

      selectCentres()
      {
        this.markers = []
        this.request('post', 'select_centres').then((response) =>
        {
          console.log(response)
          response.forEach((marker) =>
          {
            marker.infoText = this.getTemplate(
              marker.centre,
              marker.activity,
              marker.start_hour,
              marker.end_hour,
              marker.address
            )
            marker.position = {
              lat: parseFloat(marker.latitude),
              lng: parseFloat(marker.longitude)
            }
            marker.value = false
            marker.clickable = true

            this.addMarker(marker)
          })
          console.log(this.markers)
          document.querySelector('.opacity-on').classList.add('opacity-off')
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      insertCentre()
      {
        var data = {
          centre: this.centre,
          activity: this.activity,
          start_hour: this.start_hour,
          end_hour: this.end_hour,
          latitude: this.place.location.lat,
          longitude: this.place.location.lng,
          address: this.place.formatted_address,
          place_id: this.place.place_id
        }
        this.request('post', 'insert_centre', data).then((response) =>
        {
          if (response)
          {
            this.save(response)
          }
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      deleteCentre(currentID)
      {
        var data = {
          id: currentID
        }
        this.request('post', 'delete_centre', data).then((response) =>
        {
          if (response)
          {
            var index = this.markers.findIndex(item => item.id === currentID)
            this.markers.splice(index, 1)
          }
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      onOpenMarker(marker, idx)
      {
        console.info('onOpenMarker', marker, idx)
      },

      onCloseMarker(marker, idx)
      {
        console.info('onCloseMarker', marker, idx)
      },

      onCLickMarker(marker, idx)
      {
        console.info('onCLickMarker', marker, idx)
      },

      onKeyUp(event)
      {
        console.info('onKeyUp', event)
      },

      onKeyPress(event)
      {
        console.info('onKeyPress', event)
      },

      onFocus()
      {
        console.info('onFocus')
      },

      onBlur()
      {
        console.info('onBlur')
      },

      onChange()
      {
        console.info('onChange')
      },

      onPlaceChanged(place)
      {
        this.place = place
        console.info('onPlaceChanged', place)
      },

      clear()
      {
        this.$refs.autocomplete.clear()
      },

      deleteItemAsk(item)
      {
        this.dialogTitle = 'Eliminar centro'
        this.dialogSubtitle = '¿Eliminar el centro ' + item.centre + '?'
        this.dialogConfirm = true
        this.currentID = item.id
      },

      deleteItems()
      {
        this.dialogTitle = ''
        this.dialogSubtitle = ''
        this.dialogConfirm = false
        if (this.currentID !== null)
        {
          this.deleteCentre(this.currentID)
        } else
        {
          this.deleteSelectedItems()
        }
      },

      deleteItemsAsk()
      {
        this.dialogTitle = 'Eliminar centros'
        this.dialogSubtitle = '¿Eliminar los centros seleccionados?'
        this.dialogConfirm = true
      },

      deleteSelectedItems()
      {
        this.selected.forEach((item) =>
        {
          this.deleteCentre(item.id)
        })
        this.selected = []
      },

      openDialog()
      {
        this.$vuetify.goTo(0)
        document.body.scrollTo(0, 0)
        this.dialog = true
      },

      close()
      {
        this.dialog = false
        this.resetForm()
      },

      save(id)
      {
        this.addMarker({
          id: id,
          centre: this.centre,
          activity: this.activity,
          start_hour: this.start_hour,
          end_hour: this.end_hour,
          latitude: this.place.location.lat,
          longitude: this.place.location.lng,
          position: {
            lat: this.place.location.lat,
            lng: this.place.location.lng
          },
          address: this.place.formatted_address,
          place_id: this.place.place_id,
          clickable: true,
          infoText: this.getTemplate(
            this.centre,
            this.activity,
            this.start_hour,
            this.end_hour,
            this.place.formatted_address
          ),
        })
        this.centerMap(this.place.location)
        this.close()
        this.zoomMap(15)

      },

      goToMarker(marker)
      {
        this.$refs.google_maps_places_aggregator.infoWinOpen = false
        this.$refs.google_maps_places_aggregator.infoWindowPos = marker.position
        this.$refs.google_maps_places_aggregator.infoOptions.content = marker.infoText
        this.$refs.google_maps_places_aggregator.infoWinOpen = true
        this.$vuetify.goTo(0)
        document.body.scrollTo(0, 0)
        this.centerMap(marker.position)
        this.zoomMap(15)
      },

      addMarker(marker)
      {
        this.markers.push(marker)
      },

      centerMap(position)
      {
        this.center = position
      },

      zoomMap(value)
      {
        this.zoom = value
      },

      resetForm()
      {
        this.centre = ''
        this.activity = ''
        this.start_hour = null
        this.end_hour = null
        this.place = null
      }
    },

    watch:
    {
      dialog(val)
      {
        val || this.close()
      }
    },

    computed:
    {
      valid: function ()
      {
        return this.centre !== '' &&
          this.activity !== '' &&
          this.start_hour &&
          this.end_hour &&
          this.place
      },
      formTitle()
      {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      }
    }
  })
})