<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-4">

        <div
          class="card"
          v-if="currentUser">

          <div class="card-header">
            <h3 class="card-title">{{ $t('title.my-profile') }}</h3>
          </div>
          <div class="card-body">
            <form>
              <div class="row">
                <div class="col-auto">
                  <span
                    class="avatar avatar-xl"
                    :style="avatar"
                  ></span>
                </div>
                <div class="col">
                  <div class="form-group">
                    <label class="form-label">{{ $t('labels.name') }}</label>
                    <input
                      class="form-control"
                      :class="{ 'is-invalid': errors.name }"
                      placeholder=""
                      v-model="currentUser.name"
                    />
                    <div
                      v-if="errors.name"
                      class="invalid-feedback"
                    >{{ errors.name.join(', ') }}</div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">{{ $t('labels.email') }}</label>
                <input
                  class="form-control"
                  :class="{ 'is-invalid': errors.email }"
                  placeholder=""
                  v-model="currentUser.email"
                />
                <div
                  v-if="errors.email"
                  class="invalid-feedback"
                >{{ errors.email.join(', ') }}</div>
              </div>

              <div class="form-group">
                <label class="form-label">{{ $t('labels.password') }}</label>
                <input
                  type="password"
                  :class="{ 'is-invalid': errors.password }"
                  class="form-control"
                  v-model="password"
                />
                <div
                  v-if="errors.password"
                  class="invalid-feedback"
                >{{ errors.password.join(', ') }}</div>
              </div>

              <div
                class="form-group"
                v-if="password.length > 0">

                <label class="form-label">{{ $t('labels.confirm-password') }}</label>
                <input
                  type="password"
                  class="form-control"
                  v-model="confirm_password"
                />
              </div>

              <div class="form-footer">
                <button
                  class="btn btn-primary btn-block"
                  :disabled="loading"
                  type="button"
                  @click="updateProfile"
                >{{ $t('actions.update') }}</button>
              </div>
            </form>
          </div>
        </div>

      </div>

      <div class="col-lg-8">
        <form class="card">

          <div
            class="dimmer"
            :class="{ 'active': loading }">

            <div
              class="loader"
              v-show="loading"></div>

            <div
              class="dimmer-content"
              :class="{'min-height-300': loading}">

              <div class="card-body" style="padding: 0 1.5rem 1.5rem 1.5rem">

                <ul class="nav nav-tabs">

                  <li class="nav-item">
                    <a class="nav-link" :class="{'active': tab === 0}" href="#main" @click="changeTab(0)">{{ $t('tabs.main') }}</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" :class="{'active': tab === 1}" href="#slider" @click="changeTab(1)">{{ $t('tabs.slider') }}</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" :class="{'active': tab === 2}" href="#common" @click="changeTab(2)">{{ $t('tabs.common') }}</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" :class="{'active': tab === 3}" href="#meta" @click="changeTab(3)">{{ $t('tabs.meta') }}</a>
                  </li>

                </ul>

                <input autocomplete="false" name="hidden" type="text" style="display:none;">

                <!-- <h3 class="card-title">{{ $t('menu.settings') }}</h3> -->

                <div class="row" style="padding-top: 1.5rem;">

                  <!-- Tab 1 -->
                  <div class="col-12" v-show="tab === 0">

                    <div class="row">

                      <div class="col-12">
                        <div class="form-group">
                          <label for="title">{{ $t('labels.title') }}</label>
                          <input type="text" v-model="settings.main_title" class="form-control" id="title">
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <label for="text">{{ $t('labels.text') }}</label>
                          <textarea class="form-control" v-model="settings.main_text" id="text" rows="6"></textarea>
                        </div>
                      </div>

                    </div>

                  </div>

                  <!-- Tab 2 -->
                  <div class="col-12" v-show="tab === 1">

                    <div class="row">

                      <div class="col-12">
                        <div v-for="(item, index) in slider" :key="index" class="mb-3">

                          <div class="form-group">
                            <label :for="`label-${index}`">{{ $t('labels.label') }} ({{ index + 1 }})</label>
                            <input type="text" v-model="item.label" class="form-control" :id="`label-${index}`">
                          </div>

                          <div class="form-group">
                            <label :for="`title-${index}`">{{ $t('labels.title') }}</label>
                            <input type="text" v-model="item.title" class="form-control" :id="`title-${index}`">
                          </div>

                          <div class="form-group">
                            <label :for="`text-${index}`">{{ $t('labels.text') }}</label>
                            <textarea class="form-control" v-model="item.text" :id="`text-${index}`" rows="4"></textarea>
                          </div>

                          <div class="form-group">
                            <label :for="`button-${index}`">{{ $t('labels.button') }}</label>
                            <input type="text" v-model="item.button" class="form-control" :id="`button-${index}`">
                          </div>

                          <div class="form-group">
                            <label :for="`link-${index}`">{{ $t('labels.link') }}</label>
                            <input type="text" v-model="item.link" class="form-control" :id="`link-${index}`">
                          </div>

                          <div class="custom-file mb-4">
                            <input :disabled="loading"
                                   type="file"
                                   class="custom-file-input"
                                   :id="`sliderImageFile-${index}`"
                                   name="images"
                                   accept="image/*"
                                   @change="onFileChange($event, index)" />
                            <label class="custom-file-label" :for="`sliderImageFile-${index}`">{{ $t('labels.choose-image-file') }}</label>
                            <!-- <div class="invalid-feedback">This is not image file</div> -->
                          </div>

                          <div class="form-group" v-if="item.preview || item.image">
                            <div>
                              <img style="max-width: 100%; max-height: 200px;" :src="item.preview || item.image" />
                            </div>
                          </div>

                          <div>
                            <button type="button" class="btn btn-danger" @click="removeSlide(index)">{{ $t('actions.remove') }}</button>
                          </div>

                          <hr/>

                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <button type="button" class="btn btn-primary" @click="createSlide()">{{ $t('actions.create') }}</button>
                        </div>
                      </div>

                    </div>

                  </div>

                  <!-- Tab 3 -->
                  <div class="col-12" v-show="tab === 2">

                    <div class="row">

                      <div class="col-12">
                        <div class="form-group">
                          <label for="phones">{{ $t('labels.phones') }}</label>
                          <input type="text" v-model="settings.phones" class="form-control" id="phones">
                          <small id="phonesHelp" class="form-text text-muted">Example: (095) 76-54-321, (093) 12-34-567</small>
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <label for="facebook">{{ $t('labels.facebook-link') }}</label>
                          <input type="text" v-model="settings.facebook" class="form-control" id="facebook">
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <label for="instagram">{{ $t('labels.instagram-link') }}</label>
                          <input type="text" v-model="settings.instagram" class="form-control" id="instagram">
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <label for="youtube">{{ $t('labels.youtube-link') }}</label>
                          <input type="text" v-model="settings.youtube" class="form-control" id="youtube">
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <label for="telegram">{{ $t('labels.telegram-link') }}</label>
                          <input type="text" v-model="settings.telegram" class="form-control" id="telegram">
                        </div>
                      </div>

                      <div class="col-12">
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" v-model="settings.email" class="form-control" id="email">
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" v-model="settings.address" class="form-control" id="address">
                      </div>
                    </div>

                    </div>

                  </div>

                  <!-- Tab 4 -->
                  <div class="col-12" v-show="tab === 3">

                    <div class="row">

                      <div class="col-12">
                        <div class="form-group">
                          <label for="meta-title">{{ $t('labels.title') }}</label>
                          <input type="text" v-model="settings.meta_title" class="form-control" id="meta-title">
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <label for="meta-description">{{ $t('labels.description') }}</label>
                          <textarea class="form-control" v-model="settings.meta_description" id="meta-description" rows="6"></textarea>
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-group">
                          <label for="meta-keywords">{{ $t('labels.keywords') }}</label>
                          <input type="text" v-model="settings.meta_keywords" class="form-control" id="meta-keywords">
                        </div>
                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>
          </div>

          <div class="card-footer text-right">

            <button
              class="btn btn-primary"
              :disabled="loading"
              type="button"
              @click="updateSettings">{{ $t('actions.save') }}</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {

  data: function() {
    return {
      errors: [],
      loading: false,

      password: '',
      confirm_password: '',

      settings: {
        main_title: '',
        main_text: '',

        facebook: '',
        telegram: '',
        youtube: '',
        instagram: '',

        phones: '',
        address: '',
        email: '',

        meta_title: '',
        meta_description: '',
        meta_keywords: '',

      },

      slider: [],

      tab: 0
    };
  },

  computed: {
    ...mapGetters({
      currentUser: "currentUser"
    }),

    avatar: function() {
      return `background-image: url(${this.currentUser.avatar})`;
    }
  },

  created() {

    this.loading = true;

    axios.get('/api/settings')
      .then(response => {

        this.settings = {
          main_title: response.data.main_title,
          main_text: response.data.main_text,

          facebook: response.data.facebook,
          telegram: response.data.telegram,
          youtube: response.data.youtube,
          instagram: response.data.instagram,
          phones: response.data.phones,
          address: response.data.address,
          email: response.data.email,

          meta_title: response.data.meta_title,
          meta_description: response.data.meta_description,
          meta_keywords: response.data.meta_keywords,
        };

        const slider = response.data.slider || [];

        this.slider = slider.map(x => {
          return Object.assign(x, {preview: ''});
        });
      })
      .catch(e => {
        if (e.response && e.response.status === 422) {
          this.errors = e.response.data.errors;
        } else {
          this.$notify({ group: 'app', type: 'error', text: e.message });
        }
      })
      .then(() => this.loading = false);
  },

  methods: {

    changeTab(i) {
      this.tab = i
    },

    updateProfile() {

      this.loading = true;

      axios
        .put(`/api/users/${this.currentUser.id}`, {
          name: this.currentUser.name,
          email: this.currentUser.email,
          password: this.password,
          password_confirmation: this.confirm_password
        })
        .then(response => {
          this.$notify({
            group: 'app',
            type: 'success',
            text: this.$t('notifications.record-updated')
          });

          // Clear password
          this.password = '';
          this.confirm_password = '';

          this.loading = false;
        })
        .catch(e => {
          if (e.response && e.response.status === 422) {
            this.errors = e.response.data.errors;
          } else {
            this.$notify({ group: 'app', type: 'error', text: e.message });
          }

          this.loading = false;
        });
    },

    updateSettings() {

      this.loading = true;

      const data = { settings: this.settings }

      axios
        .post('/api/settings', data)
        .then(() => {
          return this.updateSlider();
        })
        .then(() => {
          this.$notify({
            group: 'app',
            type: 'success',
            text: this.$t('notifications.record-updated')
          });

          this.loading = false;
        })
        .catch(e => {
          if (e.response.status === 422) {
            this.errors = e.response.data.errors;
          } else {
            this.$notify({ group: 'app', type: 'error', text: e.message });
          }

          this.loading = false;
        });
    },

    updateSlider() {

      const formData = new FormData();

      for(let index in this.slider) {

        formData.append(`slider[${index}][label]`, this.slider[index].label || '');
        formData.append(`slider[${index}][title]`, this.slider[index].title || '');
        formData.append(`slider[${index}][text]`, this.slider[index].text || '');
        formData.append(`slider[${index}][button]`, this.slider[index].button || '');
        formData.append(`slider[${index}][link]`, this.slider[index].link || '');
        formData.append(`slider[${index}][image]`, this.slider[index].image || ''); // Original image

        if (this.slider[index].file) {
          formData.append(`slider[${index}][file]`, this.slider[index].file || '');
        }
      }
      return axios
        .post(`/api/settings/slider`, formData);
    },

    // Slider
    createSlide() {
      this.slider.push({
        title: '',
        label: '',
        text: '',
        button: '',
        link: '',
        file: '',
        image: '',
        preview: '',
      });
    },

    removeSlide(index) {
      this.slider.splice(index, 1);
    },

    // BEGIN Image
    onFileChange(e, index) {
      let $file = e.target.files[0] || e.dataTransfer.files[0];
      if (!$file) return;
      this.slider[index].file = $file;
      this.createImage($file, index);
    },

    createImage(file, index) {
      let reader = new FileReader();

      reader.onload = e => {
        this.$set(this.slider[index], 'preview', e.target.result);
        // this.settings.slider[index].image = e.target.result;
      };

      reader.readAsDataURL(file);
    },
    // END Image
    // END Slider
  }
};
</script>
