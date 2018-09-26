var BootstrapTree = {
    init: function(tree, opened, closed) {
        tree.find('li')
            .children('ul')
            .parent()
            .addClass('parent_li')
            .find('> span')
            .find('i:first')
            .on('click', BootstrapTree.toggleNode);

        $j('.parent_li').find('span:first').find('.fa:first').addClass(closed);
        $j('.parent_li').find(' > ul > li').hide();
    },

    addRoot: function(tree, element) {
        if (!element || !tree) {
            return;
        }

        var ul = $j('<ul/>')

        element.appendTo(ul);
        ul.appendTo(tree);
    },

    addNode: function(parent, element, opened, closed) {

        if (!element || !parent) {
            return;
        }

        var ul = parent.find('> ul');
        if (ul.length == 0) {
            ul = $j('<ul/>').appendTo(parent);

            parent.addClass('parent_li')
              .find('span:first')
              .find('.fa:first')
              .prop('class',opened)
              .on('click', BootstrapTree.toggleNode);
        }
        //this.openNode(ul);
        element.appendTo(ul);
    },

    removeNode: function(element) {

        if (element.prop("tagName") != 'LI') {
            return;
        }

        var ul = element.closest('ul');
        var li = ul.closest('li');

        if (ul.find('>li').length <= 1) {
            ul.remove();
            li.find('i.fa').removeClass('fa-minus-square fa-plus-square');
        } else {
            element.remove();
        }

        this.openNode(li);
    },
    
    //enleve les enfant de l' <ul> passée en paramètre
    removeSons: function(ulElement){
            ulElement.find('li').hide('fast');
        setTimeout(function(){            
            ulElement.children().remove();
        },500);
    },

    openNode: function(element) {
        element.parents('li').find('i.'+closed+':first').click();
    },

    toggleNode: function(event) { 
        var children = $j(this).closest('li.parent_li').find(' > ul > li');
        if (children.is(':visible')) {
            children.hide('fast');
            $j(this).parent().find(' > i').addClass('fa-plus-square').removeClass('fa-minus-square');
        }
        else {
            children.show('fast');
            $j(this).parent().find(' > i').addClass('fa-minus-square').removeClass('fa-plus-square');
        }
        event.stopPropagation();
        $j('.tree').find('.hideTreeElement').css('display', 'none');
    },

    findNode: function(tree, text) {
        //tree.find('i.fa-minus-square').click();
        this.openNode(tree.find("li:contains('"+text+"')"));
    }
}
