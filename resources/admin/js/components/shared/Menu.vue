<template>
  <div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
    <div class="container">
      <div class="row align-items-center">

        <div class="col-lg order-lg-first">
          <ul class="nav nav-tabs border-0 flex-column flex-lg-row">

            <li class="nav-item">
              <router-link
                class="nav-link"
                active-class="active"
                :to="{ path: '/admin/users' }"
              >
                <i class="fe fe-users"></i> {{ $t("menu.users") }}</router-link>
            </li>

            <li class="nav-item">
              <router-link
                class="nav-link"
                active-class="active"
                :to="{ name: 'articles-list' }"
              >
                <i class="fe fe-file-text"></i> {{ $t("menu.articles") }}</router-link>
            </li>

            <li class="nav-item">
              <router-link
                class="nav-link"
                active-class="active"
                :to="{ path: '/admin/vacancies' }"
              >
                <i class="fe fe-layers"></i> {{ $t("menu.vacancies") }}</router-link>
            </li>

            <li class="nav-item">
              <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-more-vertical"></i> {{ $t("menu.products") }}</a>
              <div class="dropdown-menu dropdown-menu-arrow">

                <router-link
                  class="dropdown-item"
                  active-class="active"
                  :to="{ path: '/admin/catalog' }"
                >
                  <i class="fe fe-list"></i> {{ $t("menu.catalog") }}
                </router-link>

                <router-link
                  class="dropdown-item"
                  active-class="active"
                  :to="{ path: '/admin/types' }"
                >
                  <i class="fe fe-bookmark"></i> {{ $t("menu.types") }}
                </router-link>

                <router-link
                  class="dropdown-item"
                  active-class="active"
                  :to="{ path: '/admin/categories' }"
                >
                  <i class="fe fe-grid"></i> {{ $t("menu.categories") }}
                </router-link>

                <router-link
                  class="dropdown-item"
                  active-class="active"
                  :to="{ path: '/admin/products' }"
                >
                  <i class="fe fe-shopping-bag"></i> {{ $t("menu.products") }}
                </router-link>

                <router-link
                  class="dropdown-item"
                  active-class="active"
                  :to="{ path: '/admin/sizes' }"
                >
                  <i class="fe fe-maximize-2"></i> {{ $t("menu.sizes") }}
                </router-link>

              </div>
            </li>

            <li class="nav-item">
              <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-more-vertical"></i> {{ $t("menu.orders") }}</a>
              <div class="dropdown-menu dropdown-menu-arrow">

                <router-link
                  class="dropdown-item"
                  active-class="active"
                  :to="{ path: '/admin/subscribers' }"
                >
                  <i class="fe fe-user-check"></i>
                  {{ $t("menu.subscribers") }}
                </router-link>

                <router-link
                  class="dropdown-item"
                  active-class="active"
                  :to="{ path: '/admin/orders' }"
                >
                  <i class="fe fe-check-circle"></i>
                  {{ $t("menu.orders") }}
                </router-link>

                <router-link
                  class="dropdown-item"
                  active-class="active"
                  :to="{ path: '/admin/messages' }"
                >
                  <i class="fe fe-mail"></i>
                  {{ $t("menu.messages") }}
                </router-link>

              </div>
            </li>

            <li class="nav-item">
              <router-link
                class="nav-link"
                active-class="active"
                :to="{ path: '/admin/profile' }"
              >
                <i class="fe fe-settings"></i> {{ $t("menu.settings") }}
              </router-link>
            </li>

            <li class="nav-item">
              <a href="/logout" class="nav-link"><i class="fe fe-log-out"></i> {{ $t("menu.logout") }}</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {}
</script>
