/*!
 * Nestable jQuery Plugin - Copyright (c) 2014 Ramon Smit - https://github.com/RamonSmit/Nestable
 * 
 */

(function($, window, document, undefined) {
    var hasTouch = 'ontouchstart' in window;

    /**
     * Detect CSS pointer-events property
     * events are normally disabled on the dragging element to avoid conflicts
     * https://github.com/ausi/Feature-detection-technique-for-pointer-events/blob/master/modernizr-pointerevents.js
     */
    var hasPointerEvents = (function() {
        var el = document.createElement('div'),
            docEl = document.documentElement;
        if(!('pointerEvents' in el.style)) {
            return false;
        }
        el.style.pointerEvents = 'auto';
        el.style.pointerEvents = 'x';
        docEl.appendChild(el);
        var supports = window.getComputedStyle && window.getComputedStyle(el, '').pointerEvents === 'auto';
        docEl.removeChild(el);
        return !!supports;
    })();

    var eStart = hasTouch ? 'touchstart' : 'mousedown',
        eMove = hasTouch ? 'touchmove' : 'mousemove',
        eEnd = hasTouch ? 'touchend' : 'mouseup',
        eCancel = hasTouch ? 'touchcancel' : 'mouseup',
        aItem = 'root';

    var defaults = {
        contentCallback: function(item) {return item.content || '' ? item.content : item.id;},
        listNodeName: 'ol',
        itemNodeName: 'li',
        handleNodeName: 'div',
        contentNodeName: 'span',
        rootClass: 'dd',
        listClass: 'dd-list',
        itemClass: 'dd-item',
        dragClass: 'dd-dragel',
        handleClass: 'dd-handle',
        contentClass: 'dd-content',
        collapsedClass: 'dd-collapsed',
        placeClass: 'dd-placeholder',
        noDragClass: 'dd-nodrag',
        noChildrenClass: 'dd-nochildren',
        emptyClass: 'dd-empty',
        expandBtnHTML: '<button class="dd-expand" data-action="expand" type="button">Expand</button>',
        collapseBtnHTML: '<button class="dd-collapse" data-action="collapse" type="button">Collapse</button>',
        group: 0,
        maxDepth: 5,
        threshold: 20,
        fixedDepth: false, //fixed item's depth
        fixed: false,
        includeContent: false,
        callback: function(l, e, p) {},
        onDragStart: function(l, e, p) {},
        listRenderer: function(children, options) {
            var html = '<' + options.listNodeName + ' class="' + options.listClass + '">';
            html += children;
            html += '</' + options.listNodeName + '>';

            return html;
        },
        itemRenderer: function(item_attrs, content, children, options, item) {
            var item_attrs_string = $.map(item_attrs, function(value, key) {
                return ' ' + key + '="' + value + '"';
            }).join(' ');

            var html = '<' + options.itemNodeName + item_attrs_string + '>';
            html += '<' + options.handleNodeName + ' class="' + options.handleClass + '">';
            html += '<' + options.contentNodeName + ' class="' + options.contentClass + '">';
            html += content;
            html += '</' + options.contentNodeName + '>';
            html += '</' + options.handleNodeName + '>';
            html += children;
            html += '</' + options.itemNodeName + '>';

            return html;
        }
    };

    function Plugin(element, options) {
        this.w = $(document);
        this.el = $(element);
        if(!options) {
            options = defaults;
        }
        if(options.rootClass !== undefined && options.rootClass !== 'dd') {
            options.listClass = options.listClass ? options.listClass : options.rootClass + '-list';
            options.itemClass = options.itemClass ? options.itemClass : options.rootClass + '-item';
            options.dragClass = options.dragClass ? options.dragClass : options.rootClass + '-dragel';
            options.handleClass = options.handleClass ? options.handleClass : options.rootClass + '-handle';
            options.collapsedClass = options.collapsedClass ? options.collapsedClass : options.rootClass + '-collapsed';
            options.placeClass = options.placeClass ? options.placeClass : options.rootClass + '-placeholder';
            options.noDragClass = options.noDragClass ? options.noDragClass : options.rootClass + '-nodrag';
            options.noChildrenClass = options.noChildrenClass ? options.noChildrenClass : options.rootClass + '-nochildren';
            options.emptyClass = options.emptyClass ? options.emptyClass : options.rootClass + '-empty';
        }

        this.options = $.extend({}, defaults, options);

        // build HTML from serialized JSON if passed
        if(this.options.json !== undefined) {
            this._build();
        }

        this.init();
    }

    Plugin.prototype = {

        init: function() {
            var list = this;

            list.reset();

            list.el.data('nestable-group', this.options.group);
            
            list.placeEl = $('<div class="' + list.options.placeClass + '"/>');

            $.each(this.el.find(list.options.itemNodeName), function(k, el) {
                var item = $(el),
                    parent = item.parent();
                list.setParent(item);
                if(parent.hasClass(list.options.collapsedClass)) {
                    list.collapseItem(parent.parent());
                }
            });

            list.el.on('click', 'button', function(e) {
                if(list.dragEl || (!hasTouch && e.button !== 0)) {
                    return;
                }
                var target = $(e.currentTarget),
                    action = target.data('action'),
                    item = target.parents(list.options.itemNodeName).eq(0);

                // release function on action type - wsp 7
                if(action === 'collapse') {
                    list.collapseItem(item);
                    aItem = item.attr('data-id');
                }
                if(action === 'expand') {
                    list.expandItem(item);
                    aItem = item.attr('data-id');
                }
                list.el.trigger(action);
            });

            var onStartEvent = function(e) {
                var handle = $(e.target);
                if(!handle.hasClass(list.options.handleClass)) {
                    if(handle.closest('.' + list.options.noDragClass).length) {
                        return;
                    }
                    handle = handle.closest('.' + list.options.handleClass);
                }
                if(!handle.length || list.dragEl || (!hasTouch && e.which !== 1) || (hasTouch && e.touches.length !== 1)) {
                    return;
                }
                e.preventDefault();
                list.dragStart(hasTouch ? e.touches[0] : e);
            };

            var onMoveEvent = function(e) {
                if(list.dragEl) {
                    e.preventDefault();
                    list.dragMove(hasTouch ? e.touches[0] : e);
                }
            };

            var onEndEvent = function(e) {
                if(list.dragEl) {
                    e.preventDefault();
                    list.dragStop(hasTouch ? e.changedTouches[0] : e);
                }
            };

            if(hasTouch) {
                list.el[0].addEventListener(eStart, onStartEvent, false);
                window.addEventListener(eMove, onMoveEvent, false);
                window.addEventListener(eEnd, onEndEvent, false);
                window.addEventListener(eCancel, onEndEvent, false);
            }
            else {
                list.el.on(eStart, onStartEvent);
                list.w.on(eMove, onMoveEvent);
                list.w.on(eEnd, onEndEvent);
            }

            var destroyNestable = function()
            {
                if(hasTouch) {
                    list.el[0].removeEventListener(eStart, onStartEvent, false);
                    window.removeEventListener(eMove, onMoveEvent, false);
                    window.removeEventListener(eEnd, onEndEvent, false);
                    window.removeEventListener(eCancel, onEndEvent, false);
                }
                else {
                    list.el.off(eStart, onStartEvent);
                    list.w.off(eMove, onMoveEvent);
                    list.w.off(eEnd, onEndEvent);
                }

                list.el.off('click');
                list.el.unbind('destroy-nestable');
                list.el.data("nestable", null);
            };

            list.el.bind('destroy-nestable', destroyNestable);

        },

        destroy: function () {
            this.el.trigger('destroy-nestable');
        },

        add: function (item) {
            
            var listClassSelector = '.' + this.options.listClass;
            var tree = $(this.el).children(listClassSelector);

            if (item.parent_id !== undefined) {
                tree = tree.find('[data-id="' + item.parent_id + '"]');
                delete item.parent_id;

                if (tree.children(listClassSelector).length === 0) {
                    tree = tree.append(this.options.listRenderer('', this.options))
                }

                tree = tree.find(listClassSelector);
                this.setParent(tree.parent());
            }

            tree.append(this._buildItem(item, this.options));
        },

        replace: function (item) {
            var html = this._buildItem(item, this.options);

            this._getItemById(item.id)
                .html(html);
        },

        remove: function (itemId) {
            var options = this.options;
            var buttonsSelector = '[data-action="expand"], [data-action="collapse"]';

            this._getItemById(itemId)
                .remove();

            // remove empty children lists
            var emptyListsSelector = '.' + options.listClass
                + ' .' + options.listClass + ':not(:has(*))';
            $(this.el).find(emptyListsSelector).remove();

            // remove buttons if parents do not have children
            $(this.el).find(buttonsSelector).each(function() {
                var siblings = $(this).siblings('.' + options.listClass);
                if (siblings.length === 0) {
                    $(this).remove();
                }
            });
        },

        _getItemById: function(itemId) {
            return $(this.el).children('.' + this.options.listClass)
                .find('[data-id="' + itemId + '"]');
        },

        _build: function() {
            var json = this.options.json;

            if(typeof json === 'string') {
                json = JSON.parse(json);
            }

            $(this.el).html(this._buildList(json, this.options));
        },

        _buildList: function(items, options) {
            if(!items) {
                return '';
            }

            var children = '';
            var that = this;

            $.each(items, function(index, sub) {
                children += that._buildItem(sub, options);
            });

            return options.listRenderer(children, options);
        },

        _buildItem: function(item, options) {
            function escapeHtml(text) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };

                return text + "".replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            function filterClasses(classes) {
                var new_classes = {};

                for(var k in classes) {
                    // Remove duplicates
                    new_classes[classes[k]] = classes[k];
                }

                return new_classes;
            }

            function createClassesString(item, options) {
                var classes = item.classes || {};

                if(typeof classes === 'string') {
                    classes = [classes];
                }

                var item_classes = filterClasses(classes);
                item_classes[options.itemClass] = options.itemClass;

                // create class string
                return $.map(item_classes, function(val) {
                    return val;
                }).join(' ');
            }

            function createDataAttrs(attr) {
                attr = $.extend({}, attr);

                delete attr.children;
                delete attr.classes;
                delete attr.content;

                var data_attrs = {};

                $.each(attr, function(key, value) {
                    if(typeof value === 'object') {
                        value = JSON.stringify(value);
                    }

                    data_attrs["data-" + key] = escapeHtml(value);
                });

                return data_attrs;
            }

            var item_attrs = createDataAttrs(item);
            item_attrs["class"] = createClassesString(item, options);

            var content = options.contentCallback(item);
            var children = this._buildList(item.children, options);

            return options.itemRenderer(item_attrs, content, children, options, item);
        },

        serialize: function() {
            var data, list = this, step = function(level) {
                var array = [],
                    items = level.children(list.options.itemNodeName);
                items.each(function() {
                    var li = $(this),
                        item = $.extend({}, li.data()),
                        sub = li.children(list.options.listNodeName);

                    if(list.options.includeContent) {
                        var content = li.find('.' + list.options.contentClass).html();

                        if(content) {
                            item.content = content;
                        }
                    }

                    if(sub.length) {
                        item.children = step(sub);
                    }
                    array.push(item);
                });
                return array;
            };
            data = step(list.el.find(list.options.listNodeName).first());
            return data;
        },

        asNestedSet: function() {
            var list = this, o = list.options, depth = -1, ret = [], lft = 1;
            var items = list.el.find(o.listNodeName).first().children(o.itemNodeName);

            items.each(function () {
                lft = traverse(this, depth + 1, lft);
            });

            ret = ret.sort(function(a,b){ return (a.lft - b.lft); });
            return ret;

            function traverse(item, depth, lft) {
                var rgt = lft + 1, id, pid;

                if ($(item).children(o.listNodeName).children(o.itemNodeName).length > 0 ) {
                    depth++;
                    $(item).children(o.listNodeName).children(o.itemNodeName).each(function () {
                        rgt = traverse($(this), depth, rgt);
                    });
                    depth--;
                }

                id = parseInt($(item).attr('data-id'));
                pid = parseInt($(item).parent(o.listNodeName).parent(o.itemNodeName).attr('data-id')) || '';

                if (id) {
                    ret.push({"id": id, "parent_id": pid, "depth": depth, "lft": lft, "rgt": rgt});
                }

                lft = rgt + 1;
                return lft;
            }
        },

        returnOptions: function() {
            return this.options;
        },

        serialise: function() {
            return this.serialize();
        },
        
        toHierarchy: function(options) {

            var o = $.extend({}, this.options, options),
                sDepth = o.startDepthCount || 0,
                ret = [];

            $(this.element).children(o.items).each(function() {
                var level = _recursiveItems(this);
                ret.push(level);
            });

            return ret;

            function _recursiveItems(item) {
                var id = ($(item).attr(o.attribute || 'id') || '').match(o.expression || (/(.+)[-=_](.+)/));
                if (id) {
                    var currentItem = {
                        "id": id[2]
                    };
                    if ($(item).children(o.listType).children(o.items).length > 0) {
                        currentItem.children = [];
                        $(item).children(o.listType).children(o.items).each(function() {
                            var level = _recursiveItems(this);
                            currentItem.children.push(level);
                        });
                    }
                    return currentItem;
                }
            }
        },

        toArray: function() {

            var o = $.extend({}, this.options, this),
                sDepth = o.startDepthCount || 0,
                ret = [],
                left = 2,
                list = this,
                element = list.el.find(list.options.listNodeName).first();

            var items = element.children(list.options.itemNodeName);
            items.each(function() {
                left = _recursiveArray($(this), sDepth + 1, left);
            });

            ret = ret.sort(function(a, b) {
                return (a.left - b.left);
            });

            return ret;

            function _recursiveArray(item, depth, left) {

                var right = left + 1,
                    id,
                    pid;
                var new_item = item.children(o.options.listNodeName).children(o.options.itemNodeName); /// .data()

                if (item.children(o.options.listNodeName).children(o.options.itemNodeName).length > 0) {
                    depth++;
                    item.children(o.options.listNodeName).children(o.options.itemNodeName).each(function() {
                        right = _recursiveArray($(this), depth, right);
                    });
                    depth--;
                }

                id = item.data().id;


                if (depth === sDepth + 1) {
                    pid = o.rootID;
                } else {

                    var parentItem = (item.parent(o.options.listNodeName)
                        .parent(o.options.itemNodeName)
                        .data());
                    pid = parentItem.id;

                }

                if (id) {
                    ret.push({
                        "id": id,
                        "parent_id": pid,
                        "depth": depth,
                        "left": left,
                        "right": right
                    });
                }

                left = right + 1;
                return left;
            }

        },

        reset: function() {
            this.mouse = {
                offsetX: 0,
                offsetY: 0,
                startX: 0,
                startY: 0,
                lastX: 0,
                lastY: 0,
                nowX: 0,
                nowY: 0,
                distX: 0,
                distY: 0,
                dirAx: 0,
                dirX: 0,
                dirY: 0,
                lastDirX: 0,
                lastDirY: 0,
                distAxX: 0,
                distAxY: 0
            };
            this.moving = false;
            this.dragEl = null;
            this.dragRootEl = null;
            this.dragDepth = 0;
            this.hasNewRoot = false;
            this.pointEl = null;
        },

        affected: function() {
            return aItem;
        },
        
        showItem: function() {
            return this.each(function () {
                var $parents = $(this).parents();
                $parents.each(function (i) {
                    var list = $(this).data("nestable");
                    if (list) {
                        $parents.slice(0, i).filter(list.options.itemNodeName).each(function(){
                            list.expandItem($(this));
                        });
                        return false;
                    }
                });
            });
        },
        
        expandItem: function(li) {
            li.removeClass(this.options.collapsedClass);
        },

        collapseItem: function(li) {
            var lists = li.children(this.options.listNodeName);
            if(lists.length) {
                li.addClass(this.options.collapsedClass);
            }
        },

        expandAll: function() {
            var list = this;
            list.el.find(list.options.itemNodeName).each(function() {
                list.expandItem($(this));
            });
        },

        collapseAll: function() {
            var list = this;
            list.el.find(list.options.itemNodeName).each(function() {
                list.collapseItem($(this));
            });
        },

        setParent: function(li) {
            if(li.children(this.options.listNodeName).length) {
                // make sure NOT showing two or more sets data-action buttons
                li.children('.custom-content').children('[data-action]').remove();
                li.children('.custom-content').append($(this.options.expandBtnHTML));
                li.children('.custom-content').append($(this.options.collapseBtnHTML));
                /*
                li.children('[data-action]').remove();
                li.append($(this.options.expandBtnHTML));
                li.append($(this.options.collapseBtnHTML));
                */
            }
        },

        unsetParent: function(li) {
            li.removeClass(this.options.collapsedClass);
            li.children('[data-action]').remove();
            li.children('.custom-content').children('[data-action]').remove();
            li.children(this.options.listNodeName).remove();
            li.children('.custom-content').children(this.options.listNodeName).remove();
        },

        dragStart: function(e) {
            var mouse = this.mouse,
                target = $(e.target),
                dragItem = target.closest(this.options.itemNodeName);

            var position = {};
            position.top = e.pageY;
            position.left = e.pageX;

            var continueExecution = this.options.onDragStart.call(this, this.el, dragItem, position);

            if (typeof continueExecution !== 'undefined' && continueExecution === false) {
                return;
            }

            this.placeEl.css('height', dragItem.height());

            mouse.offsetX = e.pageX - dragItem.offset().left;
            mouse.offsetY = e.pageY - dragItem.offset().top;
            mouse.startX = mouse.lastX = e.pageX;
            mouse.startY = mouse.lastY = e.pageY;

            this.dragRootEl = this.el;
            this.dragEl = $(document.createElement(this.options.listNodeName)).addClass(this.options.listClass + ' ' + this.options.dragClass);
            this.dragEl.css('width', dragItem.outerWidth());

            this.setIndexOfItem(dragItem);

            // fix for zepto.js
            //dragItem.after(this.placeEl).detach().appendTo(this.dragEl);
            dragItem.after(this.placeEl);
            dragItem[0].parentNode.removeChild(dragItem[0]);
            dragItem.appendTo(this.dragEl);

            $(document.body).append(this.dragEl);
            this.dragEl.css({
                'left': e.pageX - mouse.offsetX,
                'top': e.pageY - mouse.offsetY
            });
            // total depth of dragging item
            var i, depth,
                items = this.dragEl.find(this.options.itemNodeName);
            for(i = 0; i < items.length; i++) {
                depth = $(items[i]).parents(this.options.listNodeName).length;
                if(depth > this.dragDepth) {
                    this.dragDepth = depth;
                }
            }
        },

        setIndexOfItem: function(item, index) {
            if((typeof index) === 'undefined') {
                index = [];
            }

            index.unshift(item.index());

            if($(item[0].parentNode)[0] !== this.dragRootEl[0]) {
                this.setIndexOfItem($(item[0].parentNode), index);
            }
            else {
                this.dragEl.data('indexOfItem', index);
            }
        },

        restoreItemAtIndex: function(dragElement) {
            var indexArray = this.dragEl.data('indexOfItem'),
                currentEl = this.el;

            for(var i = 0; i < indexArray.length; i++) {
                if((indexArray.length - 1) === parseInt(i)) {
                    placeElement(currentEl, dragElement);
                    return
                }
                currentEl = currentEl[0].children[indexArray[i]];
            }

            function placeElement(currentEl, dragElement) {
                if(indexArray[indexArray.length - 1] === 0) {
                    $(currentEl).prepend(dragElement.clone());
                }
                else {
                    $(currentEl.children[indexArray[indexArray.length - 1] - 1]).after(dragElement.clone());
                }
            }
        },

        dragStop: function(e) {
            // fix for zepto.js
            //this.placeEl.replaceWith(this.dragEl.children(this.options.itemNodeName + ':first').detach());
            var el = this.dragEl.children(this.options.itemNodeName).first();
            el[0].parentNode.removeChild(el[0]);
            this.placeEl.replaceWith(el);

            var position = {};
            position.top = e.pageY;
            position.left = e.pageX;

            if(this.hasNewRoot) {
                if(this.options.fixed === true) {
                    this.restoreItemAtIndex(el);
                }
                else {
                    this.el.trigger('lostItem');
                }
                this.dragRootEl.trigger('gainedItem');
            }
            else {
                this.dragRootEl.trigger('change');
            }

            this.dragEl.remove();
            this.options.callback.call(this, this.dragRootEl, el, position);

            this.reset();
        },

        dragMove: function(e) {
            var list, parent, prev, next, depth,
                opt = this.options,
                mouse = this.mouse;

            this.dragEl.css({
                'left': e.pageX - mouse.offsetX,
                'top': e.pageY - mouse.offsetY
            });

            // mouse position last events
            mouse.lastX = mouse.nowX;
            mouse.lastY = mouse.nowY;
            // mouse position this events
            mouse.nowX = e.pageX;
            mouse.nowY = e.pageY;
            // distance mouse moved between events
            mouse.distX = mouse.nowX - mouse.lastX;
            mouse.distY = mouse.nowY - mouse.lastY;
            // direction mouse was moving
            mouse.lastDirX = mouse.dirX;
            mouse.lastDirY = mouse.dirY;
            // direction mouse is now moving (on both axis)
            mouse.dirX = mouse.distX === 0 ? 0 : mouse.distX > 0 ? 1 : -1;
            mouse.dirY = mouse.distY === 0 ? 0 : mouse.distY > 0 ? 1 : -1;
            // axis mouse is now moving on
            var newAx = Math.abs(mouse.distX) > Math.abs(mouse.distY) ? 1 : 0;

            // do nothing on first move
            if(!mouse.moving) {
                mouse.dirAx = newAx;
                mouse.moving = true;
                return;
            }

            // calc distance moved on this axis (and direction)
            if(mouse.dirAx !== newAx) {
                mouse.distAxX = 0;
                mouse.distAxY = 0;
            }
            else {
                mouse.distAxX += Math.abs(mouse.distX);
                if(mouse.dirX !== 0 && mouse.dirX !== mouse.lastDirX) {
                    mouse.distAxX = 0;
                }
                mouse.distAxY += Math.abs(mouse.distY);
                if(mouse.dirY !== 0 && mouse.dirY !== mouse.lastDirY) {
                    mouse.distAxY = 0;
                }
            }
            mouse.dirAx = newAx;

            /**
             * move horizontal
             */
            if(mouse.dirAx && mouse.distAxX >= opt.threshold) {
                // reset move distance on x-axis for new phase
                mouse.distAxX = 0;
                prev = this.placeEl.prev(opt.itemNodeName);
                // increase horizontal level if previous sibling exists, is not collapsed, and can have children
                if(mouse.distX > 0 && prev.length && !prev.hasClass(opt.collapsedClass) && !prev.hasClass(opt.noChildrenClass)) {
                    // cannot increase level when item above is collapsed
                    list = prev.find(opt.listNodeName).last();
                    // check if depth limit has reached
                    depth = this.placeEl.parents(opt.listNodeName).length;
                    if(depth + this.dragDepth <= opt.maxDepth) {
                        // create new sub-level if one doesn't exist
                        if(!list.length) {
                            list = $('<' + opt.listNodeName + '/>').addClass(opt.listClass);
                            list.append(this.placeEl);
                            prev.append(list);
                            this.setParent(prev);
                        }
                        else {
                            // else append to next level up
                            list = prev.children(opt.listNodeName).last();
                            list.append(this.placeEl);
                        }
                    }
                }
                // decrease horizontal level
                if(mouse.distX < 0) {
                    // we can't decrease a level if an item preceeds the current one
                    next = this.placeEl.next(opt.itemNodeName);
                    if(!next.length) {
                        parent = this.placeEl.parent();
                        this.placeEl.closest(opt.itemNodeName).after(this.placeEl);
                        if(!parent.children().length) {
                            this.unsetParent(parent.parent());
                        }
                    }
                }
            }

            var isEmpty = false;

            // find list item under cursor
            if(!hasPointerEvents) {
                this.dragEl[0].style.visibility = 'hidden';
            }
            this.pointEl = $(document.elementFromPoint(e.pageX - document.body.scrollLeft, e.pageY - (window.pageYOffset || document.documentElement.scrollTop)));
            if(!hasPointerEvents) {
                this.dragEl[0].style.visibility = 'visible';
            }
            if(this.pointEl.hasClass(opt.handleClass)) {
                this.pointEl = this.pointEl.closest(opt.itemNodeName);
            }
            if(this.pointEl.hasClass(opt.emptyClass)) {
                isEmpty = true;
            }
            else if(!this.pointEl.length || !this.pointEl.hasClass(opt.itemClass)) {
                return;
            }

            // find parent list of item under cursor
            var pointElRoot = this.pointEl.closest('.' + opt.rootClass),
                isNewRoot = this.dragRootEl.data('nestable-id') !== pointElRoot.data('nestable-id');

            /**
             * move vertical
             */
            if(!mouse.dirAx || isNewRoot || isEmpty) {
                // check if groups match if dragging over new root
                if(isNewRoot && opt.group !== pointElRoot.data('nestable-group')) {
                    return;
                }

                // fixed item's depth, use for some list has specific type, eg:'Volume, Section, Chapter ...'
                if(this.options.fixedDepth && this.dragDepth + 1 !== this.pointEl.parents(opt.listNodeName).length) {
                    return;
                }

                // check depth limit
                depth = this.dragDepth - 1 + this.pointEl.parents(opt.listNodeName).length;
                if(depth > opt.maxDepth) {
                    return;
                }
                var before = e.pageY < (this.pointEl.offset().top + this.pointEl.height() / 2);
                parent = this.placeEl.parent();
                // if empty create new list to replace empty placeholder
                if(isEmpty) {
                    list = $(document.createElement(opt.listNodeName)).addClass(opt.listClass);
                    list.append(this.placeEl);
                    this.pointEl.replaceWith(list);
                }
                else if(before) {
                    this.pointEl.before(this.placeEl);
                }
                else {
                    this.pointEl.after(this.placeEl);
                }
                if(!parent.children().length) {
                    this.unsetParent(parent.parent());
                }
                if(!this.dragRootEl.find(opt.itemNodeName).length) {
                    this.dragRootEl.append('<div class="' + opt.emptyClass + '"/>');
                }
                // parent root list has changed
                this.dragRootEl = pointElRoot;
                if(isNewRoot) {
                    this.hasNewRoot = this.el[0] !== this.dragRootEl[0];
                }
            }
        }

    };

    $.fn.nestable = function(params, val) {
        var lists = this,
            retval = this;

        if(!('Nestable' in window)) {
            window.Nestable = {};
            Nestable.counter = 0;
        }

        lists.each(function() {
            var plugin = $(this).data("nestable");

            if(!plugin) {
                Nestable.counter++;
                $(this).data("nestable", new Plugin(this, params));
                $(this).data("nestable-id", Nestable.counter);

            }
            else {
                if(typeof params === 'string' && typeof plugin[params] === 'function') {
                    if (typeof val !== 'undefined') {
                        retval = plugin[params](val);
                    }else{
                        retval = plugin[params]();
                    }
                }
            }
        });

        return retval || lists;
    };

})(window.jQuery || window.Zepto, window, document);
