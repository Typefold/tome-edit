(function() {
  (function($) {
    var ChapterOptions, chapterOpt;
    ChapterOptions = (function() {
      function ChapterOptions() {
        this.saveOptions();
      }

      ChapterOptions.prototype.saveOptions = function() {
        return $('.save-chapter-options').click(function(event) {
          window.activeModal.closeModal('chapter-options-modal');
          $('#publish').click();
          return window.saved_from_modal = true;
        });
      };

      return ChapterOptions;

    })();
    return chapterOpt = new ChapterOptions;
  })(jQuery);

}).call(this);

(function() {
  (function($) {
    var TomeSkinElements, classInit, previewButton;
    previewButton = $('#preview-action').clone();
    $('#preview-action').remove();
    previewButton.find('a').text('Preview');
    $('#publishing-action').prepend(previewButton);
    $('.submitdelete').text('').addClass('dashicons dashicons-trash');
    TomeSkinElements = (function() {
      function TomeSkinElements() {
        this.tomeTabs();
        this.toggleTooltips();
      }

      TomeSkinElements.prototype.tomeTabs = function() {
        return $('.tabs-nav li').click(function() {
          var sectionId;
          $(this).siblings('.active').removeClass('active');
          $(this).addClass('active');
          sectionId = $(this).data("section-id");
          return $('#' + sectionId).addClass('active').siblings('.active').removeClass('active');
        });
      };

      TomeSkinElements.prototype.toggleTooltips = function() {
        $(window).load(function() {
          return new Tooltip({
            target: $('div[aria-label="Reference"]')[0],
            openOn: 'always',
            content: 'Manage your references and bibliography',
            classes: 'tooltip-theme-arrows tooltip-hidden references-tooltip',
            position: 'top center'
          });
        });
        $('.tooltip-holder').each(function(index, el) {
          return new Tooltip({
            target: $(el)[0],
            openOn: 'always',
            content: $(this).data('tooltip-content'),
            classes: 'tooltip-theme-arrows tooltip-hidden',
            position: $(this).data('tooltip-position')
          });
        });
        return $('#toplevel_page_tome-help a').click(function(e) {
          e.preventDefault();
          $(this).toggleClass('active');
          return $('.tooltip').toggleClass('tooltip-hidden');
        });
      };

      return TomeSkinElements;

    })();
    return classInit = new TomeSkinElements();
  })(jQuery);

}).call(this);

(function() {
  (function($) {
    var TomeDashboard, dashboard;
    TomeDashboard = (function() {
      function TomeDashboard() {
        if ($('#tome-dashboard').length > 0) {
          this.init();
        }
      }

      TomeDashboard.prototype.init = function() {
        this.initList();
        return this.list_content();
      };

      TomeDashboard.prototype.list_content = function() {
        var $this;
        $this = this;
        return $('.action').click(function() {
          if ($(this).hasClass('redirect')) {
            return;
          }
          $(this).addClass('active').siblings('.active').removeClass('active');
          $('#chapters-widget').addClass('active');
          console.log($(this).find('h2').text());
          return $.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
              action: "dashboard_list",
              post_type: $(this).data('type'),
              new_link: $(this).data('new'),
              heading_text: $(this).find('h2').text()
            },
            success: function(results) {
              $('#chapters-widget').html(results);
              $this.initList();
              return $('#chapters-widget').removeClass('active');
            }
          });
        });
      };

      TomeDashboard.prototype.initList = function() {
        var chaptersList, options;
        options = {
          valueNames: ['chapter-title'],
          page: 10,
          plugins: [
            ListPagination({
              outerWindow: 1
            })
          ]
        };
        return chaptersList = new List('chapters-widget', options);
      };

      return TomeDashboard;

    })();
    return dashboard = new TomeDashboard;
  })(jQuery);

}).call(this);

(function() {
  var initListPlugin;

  initListPlugin = function(results) {
    var mediaList, options;
    options = {
      valueNames: ['media-title'],
      page: 9
    };
    mediaList = new List('embedded-media-list', options);
    return window.mediaList = mediaList;
  };

}).call(this);

(function() {
  var $, TomeModal;

  $ = jQuery;

  TomeModal = (function() {
    function TomeModal(modalId, modalAction, actionCallback) {
      this.modalId = modalId;
      this.modalAction = modalAction;
      this.modalEl = $('#' + modalId);
      this.closeElement = '.close-modal';
      this.actionCallback = false;
      if (actionCallback && typeof actionCallback === 'function') {
        this.actionCallback = actionCallback;
      }
      this.openModal(modalId, modalAction);
      this.init();
    }

    TomeModal.prototype.init = function() {
      return $('body').on('click', this.closeElement, this.closeModal);
    };

    TomeModal.prototype.openModal = function(modalId, modalAction) {
      this.addBackdrop();
      this.modalEl.addClass('active');
      this.tabs();
      if (modalAction) {
        return this.getModalContent(modalAction);
      }
      return this.modalEl.removeClass('loading');
    };

    TomeModal.prototype.getModalContent = function() {
      var _this;
      _this = this;
      return $.ajax({
        url: ajaxurl,
        method: "POST",
        data: {
          action: this.modalAction
        },
        success: function(results) {
          _this.modalEl.find('.main-content').html(results).removeClass('loading');
          if (_this.actionCallback) {
            return _this.actionCallback(results);
          }
        }
      });
    };

    TomeModal.prototype.goToTab = function(tabID) {
      this.modalEl.find('.modal-section-tab').removeClass('active');
      this.modalEl.find('.modal-section').addClass('hidden');
      this.modalEl.find('#' + tabID).addClass('active');
      return this.modalEl.find('.modal-section[tab-id="' + tabID + '"]').removeClass('hidden');
    };

    TomeModal.prototype.tabs = function() {
      return this.modalEl.on('click', '.modal-section-tab', (function(_this) {
        return function(evt) {
          var sectionId, tabEl;
          tabEl = $(evt.target);
          sectionId = tabEl.attr('id');
          _this.modalEl.find('.modal-section-tab').removeClass('active');
          tabEl.addClass('active');
          _this.modalEl.find('.modal-section').addClass('hidden');
          return _this.modalEl.find('.modal-section[tab-id="' + sectionId + '"]').removeClass('hidden');
        };
      })(this));
    };

    TomeModal.prototype.closeModal = function(modalId) {
      $('.media-modal-backdrop').remove();
      if (modalId !== 'undefined' && typeof modalId === 'string') {
        return $('#' + modalId).removeClass('active');
      }
      return $(this).parents('.tome-modal').removeClass('active');
    };

    TomeModal.prototype.addBackdrop = function() {
      if ($('.media-modal-backdrop').length === 0) {
        return $('body').append('<div class="media-modal-backdrop"></div>');
      }
    };

    TomeModal.removeBackdrop = function() {
      return $('.media-modal-backdrop').remove();
    };

    TomeModal.prototype.getOpenModals = function() {
      return $('.tome-modal.active');
    };

    return TomeModal;

  })();

  $('body').on('click', '.open-modal', function() {
    var modalAction, modalId;
    modalId = $(this).attr('data-modal-id');
    modalAction = $(this).attr('data-action');
    return window.activeModal = new TomeModal(modalId, modalAction);
  });

}).call(this);

(function() {
  (function($) {
    var TomePublishBox, publishBox;
    TomePublishBox = (function() {
      function TomePublishBox() {
        this.tomeSave();
        this.tomeDelete();
        this.showSaveButton();
        this.cancelOption();
        this.editOption();
        this.togglePasswordField();
      }

      TomePublishBox.prototype.tomeSave = function() {
        return $('.tome-publish').click(function() {
          return $('#post').submit();
        });
      };

      TomePublishBox.prototype.tomeDelete = function() {
        return $('.tome-delete-link').click(function() {
          return confirm('Are you sure, you want to delete this post?');
        });
      };

      TomePublishBox.prototype.showSaveButton = function() {
        return $('.custom-publish').find('input').change(function(data) {
          $('.save-publish-options').slideDown();
          return $('.tome-publish-actions').find('button, a').attr('disabled', '');
        });
      };

      TomePublishBox.prototype.cancelOption = function() {
        return $('.cancel-editing').click(function() {
          $('.options.active').removeClass('active').slideUp(250);
          $('.sub-wrapper').slideUp(250);
          $('.publish-cover').fadeOut(250);
          return $('.tome-publish-actions').find('button, a').removeAttr('disabled');
        });
      };

      TomePublishBox.prototype.togglePasswordField = function() {
        return $('input[name="visibility"]').change(function() {
          if ($(this).val() === 'password') {
            return $('#post_password').removeClass('hidden');
          } else {
            return $('#post_password').addClass('hidden');
          }
        });
      };

      TomePublishBox.prototype.editOption = function() {
        return $('.single-setting > .edit-link').click(function() {
          var optionsID;
          optionsID = $(this).data('options-id');
          if ($('#' + optionsID).hasClass('active') === true) {
            $('#' + optionsID).removeClass('active').slideUp(250);
            $('.sub-wrapper').slideUp(250);
            $('.publish-cover').fadeOut(250);
            return $('.tome-publish-actions').find('button, a').removeAttr('disabled');
          } else {
            $('.sub-wrapper').find('.active').slideUp(250).removeClass('active');
            $('#' + optionsID).slideDown(250).addClass('active');
            $('.sub-wrapper').slideDown(250);
            return $('.publish-cover').fadeIn(250);
          }
        });
      };

      return TomePublishBox;

    })();
    return publishBox = new TomePublishBox;
  })(jQuery);

}).call(this);
