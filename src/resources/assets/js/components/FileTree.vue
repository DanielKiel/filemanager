<template>
    <div class="row">
        <div class="col-md-4">
            <md-card>
                <md-card-header>
                    {{folder.name}} <md-button class="md-icon-button" @click="saveTree()"><md-icon>save</md-icon></md-button>
                    <md-button class="md-icon-button" @click="addChild()"><md-icon>add</md-icon></md-button>
                </md-card-header>
                <md-card-content>
                    <md-list>
                        <dion-filetree-item
                                class="item"
                                @update-tree="updateTree"
                                v-for="(data, index) in folder.children"
                                :items="data" :key="index">
                        </dion-filetree-item>

                    </md-list>
                </md-card-content>
            </md-card>


        </div>
        <div class="col-md-8">
            <div v-if="showContent">

                <dion-filegrid
                    :api="api"
                    :upload_api="upload_api"
                    :path="path">

                </dion-filegrid>

            </div>
        </div>
    </div>



</template>

<script>
    Vue.component('dion-filetree-item', require('./FileTreeItem.vue'));

    window.FileBus = new Vue();

    export default {
        props: [
            'jsonapi',
            'api',
            'upload_api'
        ],
        data: function () {
            return {
                open: false,
                showContent: false,
                folder: {
                    name: 'Filemanager',
                    children: [
                        {
                            name: 'uploads'
                        }
                    ]
                },
                path: ''
            }
        },

        created() {
            this.fetchDirStruct()

            FileBus.$on('activated', (obj) => {
                this.showContent = false
                this.getPath(obj).then( path => {
                    this.path = path

                    FileBus.$emit('path-changed', {
                        path: path
                    });

                    this.showContent = true
                })
            })
        },

        watch: {
            path: function(newVal, oldVal) {
                if (newVal !== undefined) {
                    this.$forceUpdate()
                }
            }
        },

        computed: {
            isFolder: function () {
                return this.folder.children &&
                    this.folder.children.length
            }
        },
        methods: {
            toggle: function () {
                if (this.isFolder) {
                    this.open = !this.open
                }
            },
            addChild: function () {
                let children = this.folder.children
                children.push({
                    name: 'give_it_a_name'
                })

                this.$set(this.folder, 'children', children)
            },

            fetchDirStruct() {
                axios({
                    method: 'GET',
                    url: this.jsonapi
                }).then(response => {
                    if (response.data.name === undefined) {
                        return
                    }

                    this.folder = response.data


                }).catch((error, response) => {

                })
            },

            updateTree() {
                this.saveTree()
            },

            saveTree() {
                axios({
                    method: 'POST',
                    url: this.jsonapi,
                    data: {
                        menu: this.folder
                    }
                }).then(response => {
                    //this.folder = response.data
                }).catch((error, response) => {

                })
            },

            getPath(obj) {
                return new Promise( (resolve, reject) => {
                    let path = obj._data['folder']['name']
                    let parent = obj.$parent

                    while(parent !== undefined) {

                        if (parent._data['folder'] !== undefined && parent._data['folder']['name'] !== this.folder.name) {
                            path = parent._data['folder']['name']  + '/' + path
                        }

                        parent = parent.$parent
                    }

                    resolve(path)
                } )
            }
        }
    }
</script>

<style>
    .item {
        cursor: pointer;
    }
    .bold {
        font-weight: bold;
    }
    ul {
        padding-left: 1em;
        line-height: 1.5em;
        list-style-type: dot;
    }
</style>