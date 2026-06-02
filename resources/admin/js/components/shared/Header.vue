<template>
  <div class="header py-4">
    <div class="container">
      <div class="d-flex">
        <a class="header-brand" href="/">
          <img src="./../../../images/logo.png" class="header-brand-img" alt="MetrNaMetr">
        </a>

        <div class="d-flex order-lg-2 ml-auto" v-if="currentUser">
          <div class="dropdown">
            <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
              <span class="avatar" :style="avatar"></span>
              <span class="ml-2 d-none d-lg-block">
                <span class="text-default">{{ currentUser.name }}</span>
                <small class="text-muted d-block mt-1">{{ currentUser.email }}</small>
              </span>
            </a>

            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">

              <!--
              <a class="dropdown-item" href="#">
                <i class="dropdown-icon fe fe-user"></i> Profile
              </a>
              -->

              <router-link class="dropdown-item" :to="{ path: '/admin/profile' }">
                <i class="dropdown-icon fe fe-settings"></i> {{ $t("menu.profile") }}
              </router-link>

              <!--
              <a class="dropdown-item" href="#">
                <span class="float-right"><span class="badge badge-primary">6</span></span>
                <i class="dropdown-icon fe fe-mail"></i> Inbox
              </a>
              <a class="dropdown-item" href="#">
                <i class="dropdown-icon fe fe-send"></i> Message
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">
                <i class="dropdown-icon fe fe-help-circle"></i> Need help ?
              </a>
              -->

              <a class="dropdown-item" href="/logout">
                <i class="dropdown-icon fe fe-log-out"></i> {{ $t("menu.logout") }}
              </a>
            </div>

          </div>
        </div>
        <a href="#"
          class="header-toggler d-lg-none ml-3 ml-lg-0"
          data-toggle="collapse"
          data-target="#headerMenuCollapse">
          <span class="header-toggler-icon"></span>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  computed: {
    ...mapGetters({
      currentUser: "currentUser"
    }),

    avatar: function() {
      return `background-image: url(${this.currentUser.avatar})`;
    }
  }
}
</script>
