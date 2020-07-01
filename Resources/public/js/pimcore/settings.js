pimcore.registerNS("pimcore.plugin.SintraTrainingBundle.settings");

pimcore.plugin.SintraTrainingBundle.settings = Class.create({

    bundlesPanels: {},

    initialize: function () {

        this.getData();
    },

    getData: function () {
        Ext.Ajax.request({
            url: '/admin/settings/get-bundles-settings',
            success: function (response) {

                this.data = Ext.decode(response.responseText).data;
                
                this.getTabPanel();

            }.bind(this)
        });
    },

    getTabPanel: function () {

        if (!this.panel) {

            var self = this;

            this.panel = Ext.create('Ext.panel.Panel', {
                id: 'sintra_settings',
                title: t('sintra_settings'),
                iconCls: 'pimcore_icon_system',
                border: false,
                layout: 'fit',
                closable: true
            });

            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.panel);
            tabPanel.setActiveItem('sintra_settings');

            this.layout = Ext.create('Ext.tab.Panel', {
                bodyStyle: 'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                defaults: {
                    forceLayout: true
                },
                buttons: [
                    {
                        text: t('save'),
                        handler: this.save.bind(this),
                        iconCls: 'pimcore_icon_apply'
                    }
                ]
            });

            this.data.forEach(function(bundle) {
                self.bundlesPanels[bundle.name] = self.getConfigFormForBundle(bundle);
                self.layout.add(self.bundlesPanels[bundle.name]);
            });

            this.panel.add(this.layout);

            this.layout.setActiveItem(0);

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        
        if(tabPanel.items.keys.indexOf("sintra_settings") > -1){
            tabPanel.setActiveItem('sintra_settings');
        }else{
            throw "need to reload";
        }
    },

    save: function () {
        var values = {};

        for (var bundle in this.bundlesPanels) {
            if (this.bundlesPanels.hasOwnProperty(bundle)) {
                values[bundle] = this.bundlesPanels[bundle].getForm().getFieldValues();
            }
        }

        Ext.Ajax.request({
            url: '/admin/settings/save-bundles-settings',
            method: 'post',
            params: {
                values: Ext.encode(values)
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t('success'), t('sintra_settings_save_success'), 'success');

                        Ext.MessageBox.confirm(t('info'), t('reload_pimcore_changes'), function (buttonValue) {
                            if (buttonValue === 'yes') {
                                window.location.reload();
                            }
                        }.bind(this));

                    } else {
                        pimcore.helpers.showNotification(t('error'), t('sintra_settings_save_error'),
                            'error', t(res.message));
                    }
                } catch (e) {
                    pimcore.helpers.showNotification(t('error'), t('sintra_settings_save_error'), 'error');
                }
            }
        });
    },

    getConfigFormForBundle: function (bundle) {

        var me = this,
            bundlePanel;
            
        bundlePanel = Ext.create('Ext.form.Panel', {
            title: bundle.label,
            border: false,
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true,
                listeners: {
                    render: function (el) {
                        me.checkForInheritance(el);
                    }
                }
            },
            fieldDefaults: {
                labelWidth: 250
            },
            items: []
        });
        
        for(var field in bundle.data){
            var item = new Ext.form.field.Text({
                xtype: 'textfield',
                fieldLabel: t(field),
                fieldLabelTip: t("tooltip_"+field),
                labelWidth: 200,
                name: field,
                value: bundle.data[field] ? bundle.data[field] : "",
                width: 500
            });
            
            bundlePanel.items.add(item);
        }

        return bundlePanel;
    }
});
