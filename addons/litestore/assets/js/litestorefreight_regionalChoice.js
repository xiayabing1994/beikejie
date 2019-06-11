//******* 地区选择插件 *******//
(function () {
    /***
     * 地区选择插件
     * @param container
     * @param datas
     * @constructor
     */
    function RegionalChoice(container, datas) {
        this.container = container;
        this.datas = datas;
        this.initInterface();  // 初始化地域界面
    }

    RegionalChoice.prototype = {

        /**
         * 条件渲染
         * @param alreadyIds 已存在的区域ID: 用于新增
         * @param checkedIds 已选中的区域ID: 用于编辑
         * @param
         */
        render: function (alreadyIds, checkedIds) {
            alreadyIds = alreadyIds || [];
            alreadyIds.length > 0 && this.setAlready(alreadyIds);
            checkedIds = checkedIds || [];
            checkedIds.length > 0 && this.setChecked(checkedIds);
        },

        /**
         * 初始化地域界面
         */
        initInterface: function () {
            var _this = this;
            $(_this.container).append(
                $('<div/>', {
                    class: 'place-div'
                }).append(
                    $('<div/>', {}).append(
                        $('<div/>', {
                            class: 'checkbtn'
                        })
                            .append(
                                $('<label/>', {})
                                // 全选框
                                    .append(
                                        $('<input/>', {
                                            type: 'checkbox',
                                            change: function () {
                                                var checked = $(this).is(':checked'),
                                                    $allCheckbox = $('.place').find('input[type=checkbox]');
                                                $('.ratio').html('');
                                                $allCheckbox.prop('checked', checked);
                                            }
                                        })
                                    )
                                    .append(' 全国')
                            )
                            .append(
                                $('<a/>', {
                                    class: 'clearCheck',
                                    text: '清空',
                                    click: function () {
                                        _this.destroy();
                                    }
                                })
                            )
                    ).append(
                        // 省份
                        $('<div/>', {
                            class: 'place clearfloat'
                        }).append(function () {
                            return _this.getSmallPlace();
                        }())
                    )
                )
            );

        },

        /**
         * 遍历省份
         * @returns {jQuery}
         * @constructor
         */
        getSmallPlace: function () {
            var _this = this;
            return $('<div/>', {
                class: 'smallplace clearfloat'
            }).append(
                $.map(_this.datas, function (item) {
                    return $('<div/>', {
                        class: 'place-tooltips'
                    })
                        .append(
                            $('<label/>', {})
                                .append(
                                    $('<input/>', {
                                        id: item.id,
                                        type: 'checkbox',
                                        class: 'province',
                                        change: function () {
                                            var $this = $(this)
                                                , small = $this.parent().next('.citys').find('input')
                                                , $placeTooltips = $this.parents('.place-tooltips');
                                            if ($this.prop('checked')) {
                                                small.prop('checked', true);
                                                $placeTooltips.find('.ratio').html('(' + small.length + '/' + small.length + ')');
                                            } else {
                                                small.prop('checked', false);
                                                $placeTooltips.find('.ratio').html('');
                                            }

                                        }
                                    })
                                )
                                .append(
                                    // 省份名称
                                    $('<span/>', {
                                        class: 'province_name',
                                        text: item.name
                                    })
                                )
                                .append(function () {
                                    // 城市数量
                                    if (item.city) {
                                        return $('<span/>', {
                                            class: 'ratio'
                                        })
                                    }
                                })
                        ).append(function () {
                            // 城市列表
                            if (item.city) {
                                return $('<div/>', {
                                    class: 'citys'
                                }).append(
                                    $('<i/>', {
                                        class: 'jt'
                                    }).append($('<i/>', {}))
                                ).append(
                                    _this.getSmallCitys(item.city)
                                )
                            }
                        })
                })
            )
        },

        /**
         * 遍历城市
         * @param datas
         * @returns {jQuery}
         * @constructor
         */
        getSmallCitys: function (datas) {
            return $('<div/>', {
                class: 'row-div clearfloat'
            }).append(
                $.map(datas, function (item) {
                    return $('<p/>', {}).append(
                        $('<label/>', {}).append(
                            $('<input/>', {
                                id: item.id,
                                type: 'checkbox',
                                name: 'city[]',
                                class: 'city',
                                change: function () {
                                    var $citys = $(this).parents('.citys')
                                        , $placeTooltips = $(this).parents('.place-tooltips')
                                        , tf = $citys.find('input:checked').length
                                        , $province = $placeTooltips.find('.province')
                                        , $ratio = $placeTooltips.find('.ratio');
                                    if (tf > 0) {
                                        $province.prop('checked', true);
                                        $ratio.html('(' + tf + '/' + $citys.find('input').length + ')');
                                    } else if (tf === 0) {
                                        $province.prop('checked', false);
                                        $ratio.html('');
                                    }
                                }
                            })
                        ).append(
                            $('<span/>', {
                                text: item.name
                            })
                        )
                    )
                })
            )
        },

        /**
         * 获取已选中的省市id
         * @returns {array}
         * @constructor
         */
        getCheckedIds: function () {
            var checkedIds = [];
            $('input[type=checkbox][name="city[]"]:checked').each(function (index, item) {
                checkedIds.push(item.id);
            });
            return checkedIds;
        },

        /**
         * 获取已选中的省市id (树状)
         * @returns {Array}
         */
        getCheckedTree: function () {
            var _this = this;
            // 遍历省份
            var data = [];
            $('input.province:checked').each(function (index, province) {
                var $this = $(this)
                    , $citys = $this.parent().next()
                    , $cityInputChecked = $citys.find('input.city:checked')
                    , cityData = []
                    , cityTotal = Object.keys(_this.datas[province.id].city).length;
                // 遍历城市
                if (cityTotal !== $cityInputChecked.length) {
                    $cityInputChecked.each(function (index, item) {
                        cityData.push({id: item.id, name: $(this).next().text()});
                    });
                }
                data.push({
                    id: province.id,
                    name: $this.next().text(),
                    city: cityData
                });
            });
            return data;
        },

        /**
         * 获取已选中地区内容
         * @returns {{content: string, checkedIds: *|Array}}
         */
        getCheckedContent: function () {
            // 获取已选中的省市id
            var dataTree = this.getCheckedTree()
                , checkedIds = this.getCheckedIds()
                , content = '';
            if (checkedIds.length === 373) {
                content = '全国';
            } else {
                var str = '';
                dataTree.forEach(function (item) {
                    str += item.name;
                    if (item.city.length > 0) {
                        var cityStr = '';
                        item.city.forEach(function (city) {
                            cityStr += city.name + '、';
                        });
                        str += ' (<span class="am-link-muted">'
                            + cityStr.substring(0, cityStr.length - 1) + '</span>)';
                    }
                    str += '、';
                });
                content = str.substring(0, str.length - 1);
            }
            return {
                content: content,
                ids: checkedIds
            };
        },

        /**
         * 批量选中
         * @param checkedIds 已选中的区域ID: 用于编辑
         * @constructor
         */
        setChecked: function (checkedIds) {
            var $place = $('.place-div');
            $.each(checkedIds, function (i, id) {
                $place.find('#' + id).trigger('click');
            });
        },

        /**
         * 批量删除已存在的区域
         * @param alreadyIds 已存在的区域ID: 用于新增
         * @constructor
         */
        setAlready: function (alreadyIds) {
            var $place = $('.place-div');
            $.each(alreadyIds, function (i, id) {
                var $p = $place.find('#' + id).parent().parent()
                    , $siblings = $p.siblings();
                $siblings.length > 0 ? $p.remove() : $p.closest('.place-tooltips').remove();
            });
        },

        /**
         * 清空
         */
        destroy: function () {
            var $place = $('.place-div');
            $place.find('input[type=checkbox]').prop('checked', false);
            $place.find('.ratio').html('');
        }

    };

    window.RegionalChoice = RegionalChoice;

})(window);