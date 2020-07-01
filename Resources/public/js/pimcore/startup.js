pimcore.registerNS("pimcore.plugin.SintraTrainingBundle");

pimcore.plugin.SintraTrainingBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.SintraTrainingBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);

        this.navEl = Ext.get('pimcore_menu_search').insertSibling('<li id="pimcore_menu_sintra" data-menu-tooltip="'
            + t('plugin_sintra_mainmenu') +
            '" class="pimcore_menu_item pimcore_menu_needs_children"><img src="/bundles/sintratraining/img/icon/sintra-white.svg"></li>', 'before');
        this.menu = new Ext.menu.Menu({cls: 'pimcore_navigation_flyout'});

        pimcore.layout.toolbar.prototype.sintraMenu = this.menu;
    },

    pimcoreReady: function (params, broker) {
        this.initToolbar();
    },

    initToolbar: function () {
        var toolbar = pimcore.globalmanager.get('layout_toolbar');
        var user = pimcore.globalmanager.get('user');
        
        var sintraSettingsPanelId = 'plugin_sintra_settings';

        var settingsMenu = Ext.create('Ext.menu.Item', {
            text: t('plugin_sintra_settings'),
            iconCls: 'pimcore_nav_icon_system_settings',
            hideOnClick: false,
            handler: function () {
                try {
                    pimcore.globalmanager.get(sintraSettingsPanelId).activate();
                }
                catch (e) {
                    pimcore.globalmanager.add(sintraSettingsPanelId, new pimcore.plugin.SintraTrainingBundle.settings());
                }
            }
        });

        // add to menu
        this.menu.add(settingsMenu);

        // remove main menu
        if (this.menu.items.length === 0) {
            Ext.get('pimcore_menu_sintra').remove();
            return;
        }

        this.navEl.on('mousedown', toolbar.showSubMenu.bind(toolbar.sintraMenu));
    },
    
    postOpenObject: function (object, type) {
        if (object.data.general.o_type === 'object' && object.data.general.o_className === "Test") {
            var self = this;
            
            var selectfield = object.edit.layout.query('combobox[name="selectfield"]')[0];
            var morefields = object.edit.layout.query('fieldset[name="More Fields"]')[0];
            
            this.toggleFieldSet(morefields, selectfield.value);
            
            selectfield.on("change", function(select, newVal, oldVal){
                self.toggleFieldSet(morefields, newVal);
            });
        }
    },
    
    toggleFieldSet: function(fieldset, value){       
        if(value === "show"){
            fieldset.expand();
            fieldset.setDisabled(false);
        }else{
            fieldset.collapse();
            fieldset.setDisabled(true);
        }
    }
});

var SintraTrainingBundlePlugin = new pimcore.plugin.SintraTrainingBundle();
