<template>
    <div>
        <request-loader v-if="loading" message="loading files"></request-loader>

        <md-table v-if="! loading" @md-sorted="sort" v-model="data.data" md-sort="name" md-sort-order="asc" md-card md-fixed-header>
            <md-table-toolbar>
                <h1 class="md-title">Files</h1>
                <span>Total: {{data.total}} <md-button @click="fetchFiles()"><md-icon>refresh</md-icon></md-button></span>
                <div>
                    <md-button class="md-icon-button md-raised md-primary" @click="showUpload = true">
                        <md-tooltip>Dateien hochladen</md-tooltip>
                        <md-icon>file_upload</md-icon>
                    </md-button>
                    <md-button @click="fetchAll()" :disabled="filter.published == ''">All</md-button>
                    <md-button @click="fetchSuccess()" :disabled="filter.published == '= 1'">Published</md-button>
                    <md-button @click="fetchErrors()" :disabled="filter.published == '= 0'">Non-Published</md-button>
                </div>
            </md-table-toolbar>

            <md-table-row slot="md-table-row" slot-scope="{ item }">
                <md-table-cell md-label="Name" md-sort-by="name">{{ item.name }}</md-table-cell>
                <md-table-cell md-label="Published">
                    <md-button class="md-icon-button md-primary" v-if="item.published === true" @click="changePublished(item)">
                        <md-icon>visibility</md-icon>
                    </md-button>
                    <md-button class="md-icon-button md-default" v-else @click="changePublished(item)">
                        <md-icon>visibility_off</md-icon>
                    </md-button>
                </md-table-cell>
                <md-table-cell md-label="Extension" md-sort-by="extension">{{ item.extension }}</md-table-cell>
                <md-table-cell md-label="Size" >{{ calcFilesize(item.data.size )}}</md-table-cell>
                <md-table-cell md-label="Mimetype" >{{ item.data.mimeType }}</md-table-cell>
                <md-table-cell md-label="Hochladen am" md-sort-by="created_at">{{ item.created_at }}</md-table-cell>
                <md-table-cell md-label="Preview"> <md-button @click="showPreview(item)"><md-icon>info</md-icon></md-button> </md-table-cell>
            </md-table-row>
        </md-table>

        <pub-pager v-if="data.data !== undefined" :data="data" @update="pageChange"></pub-pager>

        <md-dialog :md-active.sync="showDialog">
            <md-dialog-title>Preview</md-dialog-title>
            <md-dialog-content>
                <div v-if="! loadingDialog">

                    <!-- TODO make a preview compontent here -->
                    <span v-html="request"></span>

                </div>
                <div v-else>
                    <request-loader></request-loader>
                </div>
            </md-dialog-content>
        </md-dialog>
        <md-dialog :md-active.sync="showUpload">
            <md-dialog-title>FileUpload</md-dialog-title>
            <dion-fileupload :api="upload_api" @file:uploaded="fileUploaded" :path="path"></dion-fileupload>
        </md-dialog>

    </div>
</template>

<script>
    import filesize from 'filesize'

    Vue.component(
        'request-loader',
        require('./../Loader.vue')
    );

    export default {
        props: [
            'api',
            'upload_api',
            'path'
        ],

        data() {
            return {
                data: {},
                request: {},
                showDialog: false,
                showUpload: false,
                filter: {
                    published: '',
                    dir: this.path
                },
                hasPrevLinks: false,
                hasNextLinks: true,
                pageRange: 3,
                sortOrder: 'ASC',
                loading: false,
                loadingDialog: false
            }
        },

        created() {
            this.fetchFiles()

            if (FileBus !== undefined) {
                FileBus.$on('path-changed', (obj) => {
                    this.loading = true

                    setTimeout( () => {
                        this.filter['dir'] = obj.path
                        this.fetchFiles()
                    }, 1000)



                })
            }
        },

        methods: {
            fetchFiles() {
                this.loading = true
                axios({
                    method: 'GET',
                    url: this.api,
                    params: this.filter
                }).then(response => {
                    this.data = response.data
                    this.loading = false
                    this.$forceUpdate()

                }).catch(err => {
                    console.log(err)
                })
            },
            fetchAll() {
                this.filter['published'] = ''

                this.fetchFiles()
            },
            fetchSuccess() {
                this.filter['published'] = '= 1'

                this.fetchFiles()
            },

            fetchErrors() {
                this.filter['published'] = '= 0'

                this.fetchFiles()
            },

            sort(sorted) {
                if (this.sortOrder === 'ASC') {
                    this.sortOrder = 'DESC'
                }
                else {
                    this.sortOrder = 'ASC'
                }

                this.filter['orderBy'] = sorted + ' ' + this.sortOrder

                this.fetchFiles()
            },

            changePublished(file) {
                axios({
                    method: 'put',
                    url: '/services/filemanager/v1/' + file.id,
                    data: {
                        published: !file.published
                    }
                }).then(response => {
                    file.published = ! file.published
                    console.log(response)

                }).catch(err => {
                    console.log(err)
                })
            },

            showPreview(file) {
                this.loadingDialog = true
                this.showDialog = true

                axios({
                    method: 'GET',
                    url: '/services/filemanager/preview/v1/' + file.id
                }).then(response => {
                    this.request = response.data//window.atob(response.data)
                    this.loadingDialog = false

                }).catch(err => {
                    console.log(err)
                })
            },

            pageChange(response) {
                this.data = response.data;
            },

            calcFilesize(size) {
                return filesize(size)
            },

            fileUploaded(file) {
                this.fetchFiles()
            }
        }
    }
</script>

<style>

</style>