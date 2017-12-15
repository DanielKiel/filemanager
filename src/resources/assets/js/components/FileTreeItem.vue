<template>
    <md-list-item :md-expand="isFolder" :class="{active: active, inset: inset}">
        <md-button @click="activate()" class="md-icon-button md-list-action">
            <md-icon>cloud</md-icon>
        </md-button>
        <md-button @click="changeType()" class="md-icon-button md-list-action">
            <md-icon>edit</md-icon>
        </md-button>
        <md-button @click="addChild" class="md-icon-button md-list-action reduceMargin">
            <md-icon>add</md-icon>
        </md-button>
        <md-field v-if="type === 'edit'">
            <label>Ordnername</label>
            <md-input v-model="folder.name"></md-input>
            <md-button @click="updateTree" class="md-icon-button"><md-icon>save</md-icon></md-button>
        </md-field>
        <span class="md-list-item-text" v-else>
             {{folder.name}}
        </span>

        <md-list slot="md-expand">
            <dion-filetree-item
                    class="item"
                    @update-tree="updateTree"
                    v-for="(data, index) in folder.children"
                    :items="data" :key="id + '_' + index">
            </dion-filetree-item>
        </md-list>

    </md-list-item>



</template>

<script>
    import SnakeCase from 'snake-case'

    export default {
        props: [
            'items'
        ],
        data: function () {
            return {
                open: false,
                folder: this.items,
                type: 'see',
                active: false,
                id: this.uuid(),
                inset: true
            }
        },

        created() {
            FileBus.$on('activated', (obj) => {
                if (this.id !== obj.id) {
                    this.active = false
                }
            })
        },

        computed: {
            isFolder: function () {
                return this.folder.children !== undefined
            }
        },
        methods: {
            uuid() {
                return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                ).toLowerCase()
            },
            activate: function () {
                this.active = ! this.active

                if (this.active === true) {
                    FileBus.$emit('activated', this)
                }
            },
            changeType: function () {
                this.type = this.type === 'see' ? 'edit' : 'see'
            },

            addChild: function () {
                if (this.folder.children === undefined) {
                    this.$set(this.folder, 'children', new Array())
                }

                this.folder.children.push({
                    name: 'give_folder_a_name'
                })
            },

            updateTree() {
                this.changeType()
                this.folder.name = SnakeCase(this.folder.name)
                this.$emit('update-tree')
            }
        }
    }
</script>

<style>
    .active {
        background: gray;
    }

    .inset {
        margin-left: 10px;
    }

    .reduceMargin {
        margin-left:-7px !important;
        margin-right: 7px !important;
    }
</style>