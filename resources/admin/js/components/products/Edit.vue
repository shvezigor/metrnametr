<template>
  <div class="container">
    <div class="row row-cards row-deck">
      <div class="col-12">
        <div class="card">
          <div class="card-header justify-content-between">
            <h3 class="card-title">{{ $t('title.edit-record') }}</h3>
            <div>
              <button
                type="button"
                @click="update()"
                :disabled="loading"
                class="btn btn-primary ml-auto"
              >{{ $t('actions.save') }}</button>
              <button
                type="button"
                @click="cancel()"
                :disabled="loading"
                class="btn btn-secondary ml-auto"
              >{{ $t('actions.cancel') }}</button>
            </div>

          </div>

          <div class="card-body">
            <form>

              <!-- Set user -->
              <div class="form-group">
                <label class="form-label">
                  {{ $t('labels.user') }}
                </label>

                <model-list-select
                  v-model="record.user_id"
                  :list="users"
                  option-value="id"
                  option-text="name"
                  :placeholder="$t('labels.select-user')"
                />

                <div
                  v-if="errors.user"
                  class="invalid-feedback"
                >{{ errors.user.join(', ') }}</div>
              </div>
              <!-- END Set user -->

              <!-- Set category -->
              <div class="form-group">
                <label class="form-label">{{ $t('labels.category') }}</label>

                <multi-list-select
                  :selectedItems="selectedCategories"
                  :list="categories"
                  @select="handleSelectCategory"
                  option-value="id"
                  option-text="title"
                  :placeholder="$t('labels.select-categories')"
                />
              </div>
              <!-- END Set category -->

              <div class="form-group">
                <label>{{ $t('labels.title') }} <span class="required">*</span></label>
                <input type="text"
                       class="form-control"
                       name="title"
                       v-model="record.title"
                       :class="{ 'is-invalid': errors.title }"
                       :placeholder="$t('labels.title')"/>
                <div v-if="errors.title"
                     class="invalid-feedback">{{ errors.title.join(', ') }}
                </div>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.alias') }} <span class="required">*</span></label>
                <input type="text"
                       class="form-control"
                       name="alias"
                       v-model="record.alias"
                       :class="{ 'is-invalid': errors.alias }"
                       :placeholder="$t('labels.alias')"/>
                <div v-if="errors.alias"
                     class="invalid-feedback">{{ errors.alias.join(', ') }}
                </div>
              </div>

              <!-- Set sizes -->
              <div class="form-group">
                <label class="form-label">{{ $t('labels.size') }}</label>

                <multi-list-select
                  :selectedItems="selectedSizes"
                  :list="sizes"
                  @select="handleSelectSize"
                  option-value="id"
                  option-text="title"
                  :placeholder="$t('labels.select-sizes')"
                />
              </div>
              <!-- END Set sizes -->

              <div class="form-group">
                <label for="label">{{ $t('labels.label') }}</label>
                <select class="form-control" v-model="record.label" id="label">
                  <option v-for="(label, index) in labels" :key="index" :value="index">{{ label }}</option>
                </select>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.price') }}</label>
                <input type="text"
                       class="form-control"
                       name="price"
                       v-model="record.price"
                       :class="{ 'is-invalid': errors.price }"
                       :placeholder="$t('labels.price')"/>
                <div v-if="errors.price"
                     class="invalid-feedback">{{ errors.price.join(', ') }}
                </div>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.text') }}</label>
                <vue-editor v-model="record.text" id="product-text-editor"></vue-editor>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.description') }}</label>
                <textarea rows="10"
                          class="form-control"
                          name="description"
                          v-model="record.description"
                          :class="{ 'is-invalid': errors.description }"
                          :placeholder="$t('labels.description')"></textarea>
                <div v-if="errors.description"
                     class="invalid-feedback">{{ errors.description.join(', ') }}
                </div>
              </div>

              <div class="form-group">
                <label>{{ $t('labels.keywords') }}</label>
                <textarea rows="10"
                          class="form-control"
                          name="keywords"
                          v-model="record.keywords"
                          :class="{ 'is-invalid': errors.keywords }"
                          :placeholder="$t('labels.keywords')"></textarea>
                <div v-if="errors.keywords"
                     class="invalid-feedback">{{ errors.keywords.join(', ') }}
                </div>
              </div>

              <div class="form-group">
                <label>SEO title</label>
                <input type="text" class="form-control" v-model="record.seo_title" placeholder="SEO title" />
              </div>

              <div class="form-group">
                <label>SEO description</label>
                <textarea rows="4" class="form-control" v-model="record.seo_description" placeholder="SEO description"></textarea>
              </div>

              <div class="form-group">
                <label>Canonical URL</label>
                <input type="text" class="form-control" v-model="record.canonical_url" placeholder="https://metrnametr.com.ua/product/..." />
              </div>

              <div class="form-group">
                <label>OG image URL</label>
                <input type="text" class="form-control" v-model="record.og_image" placeholder="/storage/products/image.jpg" />
              </div>

              <div class="form-group">
                <label>FAQ JSON</label>
                <textarea rows="8" class="form-control" v-model="record.faq" placeholder='[{"question":"Питання","answer":"Відповідь"}]'></textarea>
              </div>

              <div class="form-group">
                <label>Product extra fields JSON</label>
                <textarea rows="10" class="form-control" v-model="record.extra_fields" placeholder='{"audience":"...","benefits":["..."],"specs":{"Матеріали":"..."}}'></textarea>
              </div>

              <div class="form-group">
                <div class="form-label">{{ $t('labels.published') }}</div>
                <div class="custom-switches-stacked">
                  <label class="custom-switch">
                    <input type="checkbox"
                           name="published"
                           v-model="record.published"
                           value="1"
                           :disabled="loading"
                           class="custom-switch-input" />
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description" v-if="record.published">{{ $t('labels.enable') }}</span>
                    <span class="custom-switch-description" v-if="!record.published">{{ $t('labels.disable') }}</span>
                  </label>
                </div>
              </div>

              <div class="form-group">
                <div class="form-label">{{ $t('labels.slider') }}</div>
                <div class="custom-switches-stacked">
                  <label class="custom-switch">
                    <input type="checkbox"
                           name="published"
                           v-model="record.slider"
                           value="1"
                           :disabled="loading"
                           class="custom-switch-input" />
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description" v-if="record.slider">{{ $t('labels.enable') }}</span>
                    <span class="custom-switch-description" v-if="!record.slider">{{ $t('labels.disable') }}</span>
                  </label>
                </div>
              </div>

              <div class="custom-file mb-4">
                <input multiple
                       :disabled="loading"
                       type="file"
                       class="custom-file-input"
                       id="productImageFile"
                       name="images"
                       accept="image/*"
                       @change="onFileChange" >
                <label class="custom-file-label" for="productImageFile">{{ $t('labels.choose-image-file') }}</label>
              </div>

            </form>

            <div class="row">

              <div class="col-md-4 col-sx-12 mb-4 d-flex flex-column align-items-center" v-for="(image, index) of images" :key="index">
                <div class="mb-2">
                  <img style="max-width: 200px; max-height: 200px;" :src="image" />
                </div>
                <button type="button" class="btn btn-danger" @click="removeFile(index)">{{ $t('actions.remove') }}</button>
              </div>

              <div v-if="record.images.length > 0" class="col-md-4 col-sx-12 mb-4 d-flex flex-column align-items-center" v-for="(image, index) of record.images" :key="index">
                <div class="mb-2">
                  <img style="max-width: 200px; max-height: 200px;" :src="image.location" />
                </div>
                <button type="button" class="btn btn-danger" @click="removeExistFile(image.id, index)">{{ $t('actions.remove') }}</button>
              </div>

            </div>

          </div>

          <div class="card-footer text-right">
            <div class="d-flex justify-content-end">
              <div>
                <button
                  type="button"
                  @click="update()"
                  :disabled="loading"
                  class="btn btn-primary ml-auto"
                >{{ $t('actions.save') }}</button>
                <button
                  type="button"
                  @click="cancel()"
                  :disabled="loading"
                  class="btn btn-secondary ml-auto"
                >{{ $t('actions.cancel') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from "axios";
  import { VueEditor } from "vue2-editor";
  import { ModelListSelect, MultiListSelect } from 'vue-search-select';

  export default {
    components: {
      VueEditor,
      ModelListSelect,
      MultiListSelect,
    },

    data() {
      return {
        recordID: null,
        errors: [],

        record: {
          title: '',
          alias: '',
          price: '',
          text: '',
          description: '',
          keywords: '',
          seo_title: '',
          seo_description: '',
          canonical_url: '',
          og_image: '',
          faq: '',
          extra_fields: '',
          user_id: '',
          images: [],
          published: false,
          slider: false,
        },

        loading: false,
        users: [],

        // For multi-select
        categories: [],
        selectedCategories: [],
        // END For multi-select

        // For multi-select
        sizes: [],
        selectedSizes: [],
        // END For multi-select

        images: [],
        files: [],
        imagesForRemove: [],

        labels: [],
      };
    },

    created() {
      // Get ID
      this.recordID = this.$route.params.id;
      this.loading = true;

      Promise
        .allSettled([
          this.load(),
          this.getUsers(),
          this.getCategories(),
          this.getLabels(),
          this.getSizes(),
        ])
        .finally(() => {
          this.loading = false;
        })
    },

    beforeRouteUpdate(to, from, next) {
      this.recordID = to.params.id;
      this.load();
      next();
    },

    methods: {
      getUsers() {
        axios
          .get(`/api/users/list/array`)
          .then((response) => {
            this.users = response.data;
          })
          .catch((e) => {
            this.$notify({group: "app", type: "error", text: e.message});
          });
      },

      getCategories() {
        axios
          .get('/api/categories/list/array')
          .then((response) => {
            this.categories = response.data;
          })
          .catch((e) => {
            this.$notify({ group: 'app', type: 'error', text: e.message });
          });
      },

      getLabels() {
        axios
          .get('/api/products/labels')
          .then((response) => {
            this.labels = response.data;
          })
          .catch((e) => {
            this.$notify({ group: 'app', type: 'error', text: e.message });
          });
      },

      getSizes() {
        axios
          .get('/api/sizes/list/array')
          .then((response) => {
            this.sizes = response.data;
          })
          .catch((e) => {
            this.$notify({ group: 'app', type: 'error', text: e.message });
          });
      },

      handleSelectCategory(items) {
        this.selectedCategories = items
      },

      handleSelectSize(items) {
        this.selectedSizes = items
      },

      load() {
        axios
          .get(`/api/products/${this.recordID}`)
          .then(response => {
            this.record = response.data;
            this.selectedCategories = response.data.categories;
            this.selectedSizes = response.data.sizes;
          })
          .catch(e => {
            if (e.response.status === 422) {
              this.errors = e.response.data.errors;
            } else {
              this.$notify({group: "app", type: "error", text: e.message});
            }
          })
      },

      update() {
        let form = this.record;
        this.loading = true;

        if (this.selectedCategories.length > 0) {
          form.categories = this.selectedCategories.map(x => x.id)
        }

        if (this.selectedSizes.length > 0) {
          form.sizes = this.selectedSizes.map(x => x.id)
        }

        axios
          .put(`/api/products/${form.id}`, form)

          // Upload new files
          .then((response) => {

            if (this.files.length > 0) {
              const formData = new FormData();

              if (this.files.length > 0) {
                for (let file of this.files) {
                  formData.append('images[]', file);
                }
              }

              return axios
                .post(`/api/products/${form.id}/upload`, formData);
            }

            return response;
          })

          // Remove files
          .then(() => {
            if (this.imagesForRemove.length > 0) {

              let requestsForImages = [];

              for (let imageID of this.imagesForRemove) {
                requestsForImages.push(axios.delete(`/api/images/${imageID}`));
              }

              return Promise.all(requestsForImages);
            }
          })

          // Finish
          .then(() => {
            this.$notify({
              group: 'app',
              type: 'success',
              text: this.$t('notifications.record-updated')
            });

            this.$router.push({name: 'products-list'});
          })
          .catch(e => {
            if (e.response.status === 422) {
              this.errors = e.response.data.errors;
            } else {
              this.$notify({group: 'app', type: 'error', text: e.message});
            }
          })
          .then(() => {
            this.loading = false;
          });
      },

      cancel: function() {
        this.$router.push({ name: 'products-list' });
      },

      // BEGIN Image
      onFileChange(e) {
        let files = e.target.files || e.dataTransfer.files;
        if (!files.length) return;

        for(let file of files) {
          this.files.push(file);
          this.createImage(file);
        }
      },

      createImage(file) {
        let reader = new FileReader();

        reader.onload = e => {
          this.images.push(e.target.result);
        };

        reader.readAsDataURL(file);
      },

      removeFile: function(index) {
        this.images.splice(index, 1);
        this.files.splice(index, 1);
      },

      removeExistFile(id, index) {
        if (!window.confirm("Are you sure?")) {
          return false;
        }

        this.imagesForRemove.push(id);
        this.record.images.splice(index, 1);
      },
      // END Image
    }
  }
</script>
