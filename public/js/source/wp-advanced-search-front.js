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
  Vue.component('VuetifyAdvanceSearch', VuetifyAdvanceSearch.VuetifyAdvanceSearch)

  Vue.directive('click-outside', {
    bind: function (el, binding, vnode)
    {
      el.clickOutsideEvent = function (event)
      {
        if (!(el === event.target || el.contains(event.target)))
        {
          vnode.context[binding.expression](event)
        }
      }
      document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind: function (el)
    {
      document.body.removeEventListener('click', el.clickOutsideEvent)
    }
  })

  if (document.querySelector('#app'))
  {
    new Vue({
      el: '#app',
      data: vm => ({
        options: {},
        categories: [],
        needle: '',
        customNeedle: '',
        matches: [],
        allResultsUrl: 'https://site.com/post-2/',
        isPaginated: false,
        isNextPage: false,
        pageNumber: 0,
        size: 10,
        offset: 0
      }),

      mounted()
      {
        setTimeout(function ()
        {
          document.querySelector('.opacity-on').classList.add('opacity-off')
        }, 100);
        this.options = this.$refs.vuetify_advance_search.customOptions
        this.categories = this.$refs.vuetify_advance_search.customCategories
      },

      methods: {

        onChangeCategories(selectedCategories)
        {
          this.matches = []
          this.categories = selectedCategories
          console.log('onChangeCategories', selectedCategories)
          this.searchBy()
        },

        onChangeOptions(selectedOptions)
        {
          this.matches = []
          this.options = selectedOptions
          console.log('onChangeOptions', selectedOptions)
          this.searchBy()
        },

        onChangeMatches(matches)
        {
          console.log('onChangeMatches')
        },

        onChangeNeedle(needle)
        {
          this.matches = []
          this.needle = needle
          console.log('onChangeNeedle')
          this.searchBy()
        },

        onExpandResult(item, offset)
        {
          if (item.results.length === 0)
          {
            this.getSegmentedResults(item, offset)
          }
        },

        onNextPage(offset)
        {
          this.isNextPage = true
          this.offset = offset
          this.searchBy()
        },

        onClickLastTab(item, offset)
        {
          this.getSegmentedResults(item, offset)
        },

        searchBy()
        {
          let data = {}
          if (typeof this.categories === 'string')
          {
            data.categories = this.categories
          } else
          {
            data.categories = []
            this.categories.forEach((item) =>
            {
              data.categories.push(item.id)
            })
            data.categories = data.categories.join(',')
          }
          for (let index in this.options)
          {
            data[index] = this.options[index]
          }
          data.needle = this.needle.length > 0 ? this.needle : this.customNeedle
          data.offset = this.offset
          this.request('post', 'search_on_posts', data).then((response) =>
          {
            console.log(response)
            if (response.length > 0)
            {
              response.forEach((item) =>
              {
                this.matches.push(item)
              })
              if (this.isNextPage)
              {
                this.$refs.vuetify_advance_search.nextPage()
                this.isNextPage = false
              }
            }
            if (this.matches.length === 0)
            {
              this.$refs.vuetify_advance_search.noResults = true
              this.$refs.vuetify_advance_search.showResults = true
            } else
            {
              this.$refs.vuetify_advance_search.noResults = false
            }
            this.$refs.vuetify_advance_search.loader = false
          }).catch((error) =>
          {
            console.log(error)
          })
        },

        getSegmentedResults(item, offset)
        {
          let data = {}
          data.id = item.id
          data.categories = []
          this.categories.forEach((item) =>
          {
            data.categories.push(item.id)
          })
          data.categories = data.categories.join(',')
          for (let index in this.options)
          {
            data[index] = this.options[index]
          }
          data.needle = this.needle.length > 0 ? this.needle : this.customNeedle
          data.offset = offset

          this.request('post', 'get_segmented_results', data).then((response) =>
          {
            console.log('RESPONSE', response)
            if (response.length > 0)
            {
              response.forEach((result) =>
              {
                item.results.push(result)
              })
            } else
            {
              this.$refs.vuetify_advance_search.hideResultsLoader()
            }
          }).catch((error) =>
          {
            console.log(error)
          })
        },

        getLoaderImage()
        {
          return wordpressAdvancedSearchFront.location_loader_gif
        },

        getErrorImage()
        {
          return wordpressAdvancedSearchFront.location_error_gif
        },

        request(type, action, params)
        {
          var data = new URLSearchParams()
          params = params || {}
          data.append('action', action)
          data.append('nonce', wordpressAdvancedSearchFront.nonce)
          for (var key in params)
          {
            data.append(key, params[key])
            console.log(key, params[key])
          }
          return new Promise((resolve, reject) =>
          {
            return axios({
              method: type,
              url: wordpressAdvancedSearchFront.ajax_url,
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
        }
      }
    })
  }
  if (document.querySelector('#results'))
  {
    new Vue({
      el: '#results',
      data: vm => ({
        options: {},
        categories: [],
        needle: '',
        customNeedle: '',
        matches: [],
        allResultsUrl: 'https://site.com/post-2/',
        isPaginated: false,
        isNextPage: false,
        pageNumber: 0,
        size: 10,
        offset: 0
      }),

      mounted()
      {
        setTimeout(function ()
        {
          document.querySelector('.opacity-on').classList.add('opacity-off')
        }, 100);
        this.options = this.$refs.vuetify_advance_search.customOptions
        this.categories = this.$refs.vuetify_advance_search.customCategories
      },

      methods: {

        onChangeCategories(selectedCategories)
        {
          this.matches = []
          this.categories = selectedCategories
          console.log('onChangeCategories', selectedCategories)
          this.searchBy()
        },

        onChangeOptions(selectedOptions)
        {
          this.matches = []
          this.options = selectedOptions
          console.log('onChangeOptions', selectedOptions)
          this.searchBy()
        },

        onChangeMatches(matches)
        {
          console.log('onChangeMatches')
        },

        onChangeNeedle(needle)
        {
          this.matches = []
          this.needle = needle
          console.log('onChangeNeedle')
          this.searchBy()
        },

        onExpandResult(item, offset)
        {
          if (item.results.length === 0)
          {
            this.getSegmentedResults(item, offset)
          }
        },

        onNextPage(offset)
        {
          this.isNextPage = true
          this.offset = offset
          this.searchBy()
        },

        onClickLastTab(item, offset)
        {
          this.getSegmentedResults(item, offset)
        },

        searchBy()
        {
          let data = {}
          if (typeof this.categories === 'string')
          {
            data.categories = this.categories
          } else
          {
            data.categories = []
            this.categories.forEach((item) =>
            {
              data.categories.push(item.id)
            })
            data.categories = data.categories.join(',')
          }
          for (let index in this.options)
          {
            data[index] = this.options[index]
          }
          data.needle = this.needle.length > 0 ? this.needle : this.customNeedle
          data.offset = this.offset
          this.request('post', 'search_on_posts', data).then((response) =>
          {
            console.log(response)
            if (response.length > 0)
            {
              response.forEach((item) =>
              {
                this.matches.push(item)
              })
              if (this.isNextPage)
              {
                this.$refs.vuetify_advance_search.nextPage()
                this.isNextPage = false
              }
            }
            if (this.matches.length === 0)
            {
              this.$refs.vuetify_advance_search.noResults = true
              this.$refs.vuetify_advance_search.showResults = true
            } else
            {
              this.$refs.vuetify_advance_search.noResults = false
            }
            this.$refs.vuetify_advance_search.loader = false
          }).catch((error) =>
          {
            console.log(error)
          })
        },

        getSegmentedResults(item, offset)
        {
          let data = {}
          data.id = item.id
          data.categories = []
          this.categories.forEach((item) =>
          {
            data.categories.push(item.id)
          })
          data.categories = data.categories.join(',')
          for (let index in this.options)
          {
            data[index] = this.options[index]
          }
          data.needle = this.needle.length > 0 ? this.needle : this.customNeedle
          data.offset = offset

          this.request('post', 'get_segmented_results', data).then((response) =>
          {
            console.log('RESPONSE', response)
            if (response.length > 0)
            {
              response.forEach((result) =>
              {
                item.results.push(result)
              })
            } else
            {
              this.$refs.vuetify_advance_search.hideResultsLoader()
            }
          }).catch((error) =>
          {
            console.log(error)
          })
        },

        getLoaderImage()
        {
          return wordpressAdvancedSearchFront.location_loader_gif
        },

        getErrorImage()
        {
          return wordpressAdvancedSearchFront.location_error_gif
        },

        request(type, action, params)
        {
          var data = new URLSearchParams()
          params = params || {}
          data.append('action', action)
          data.append('nonce', wordpressAdvancedSearchFront.nonce)
          for (var key in params)
          {
            data.append(key, params[key])
            console.log(key, params[key])
          }
          return new Promise((resolve, reject) =>
          {
            return axios({
              method: type,
              url: wordpressAdvancedSearchFront.ajax_url,
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
        }
      }
    })
  }
})