!function (a) {
  "use strict";
  a.pandalocker.data || (a.pandalocker.data = {}), a.pandalocker.data.__tweetedUrl = null, a.pandalocker.data.__tweetWindow = null;
  var b = a.pandalocker.tools.extend(a.pandalocker.entity.socialButton);
  b.name = "twitter-tweet", b.verification = {container: "iframe", timeout: 6e5}, b._defaults = {
    doubleCheck: !1,
    url: null,
    text: null,
    via: null,
    related: null,
    count: "horizontal",
    lang: "en",
    counturl: null,
    size: "medium"
  }, b.prepareOptions = function () {
    if (!this.options.url && !this.networkOptions.url && a("link[rel='canonical']").length > 0 && (this.options.url = a("link[rel='canonical']").attr("href")), this.url = this._extractUrl(), "vertical" === this.groupOptions.layout ? this.showError(a.pandalocker.lang.errors.unsupportedTwitterTweetLayout) : this.groupOptions.counters || (this.options.count = "none"), this.groupOptions.lang) {
      var b = this.groupOptions.lang.split("_");
      this.options.lang = b[0]
    }
    if (!this.options.text) {
      var c = a("title");
      this.options.text = c.length > 0 ? a(c[0]).text() : ""
    }
  }, b.setupEvents = function () {
    var b = this;
    a(document).bind("onp-sl-twitter-tweet", function () {
      b.url === a.pandalocker.data.__tweetedUrl && (a.pandalocker.data.__tweetWindow && a.pandalocker.data.__tweetWindow.close && a.pandalocker.data.__tweetWindow.close(), a.pandalocker.data.__tweetWindow = null, b.unlock("button", b.name, b.url))
    })
  }, b.renderButton = function (b) {
    var c = this;
    this.button = a('<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>').appendTo(b), this.button.attr("data-url", this.url), this.button.attr("data-show-count", this.options.showCount), this.options.via && this.button.attr("data-via", this.options.via), this.options.text && this.button.attr("data-text", this.options.text), this.options.lang && this.button.attr("data-lang", this.options.lang), this.options.hashtags && this.button.attr("data-hashtags", this.options.hashtags), this.options.size && this.button.attr("data-size", this.options.size), this.options.dnt && this.button.attr("data-dnt", this.options.dnt);
    var d = a("<div class='onp-sl-feature-overlay'></div>").appendTo(b);
    d.click(function () {
      var b = c.tweet(c.options.doubleCheck);
      b.done(function () {
        a(document).trigger("onp-sl-twitter-tweet", [c.url])
      })
    }), b.data("url-to-verify", c.url);
    var e = 5, f = function () {
      if (!(b.find("iframe").length > 0)) if (window.twttr.widgets && window.twttr.widgets.load) window.twttr.widgets.load(b[0]); else {
        if (0 >= e) return;
        e--, setTimeout(function () {
          f()
        }, 1e3)
      }
    };
    f()
  }, b.tweet = function (b) {
    var c = this, d = a.Deferred();
    if (b) return this.connect(function () {
      var b = c.tweet(!1);
      b.done(function () {
        var b = c.checkTweet(c.url);
        b.done(function () {
          d.resolve()
        }), b.fail(function () {
          c.showNotice(a.pandalocker.lang.errors.tweetNotFound)
        })
      })
    }), d;
    var e = [];
    if (c.options.text) {
      var f = encodeURI(c.options.text);
      f = f.replace(/#/g, "%23"), f = f.replace(/\|/g, "-"), f = f.replace(/\&/g, "%26"), e.push(["text", f])
    }
    c.options.hashtags && e.push(["hashtags", c.options.hashtags]), c.options.via && e.push(["via", c.options.via]), c.options.related && e.push(["via", c.options.related]), e.push(["url", c.url]), a.pandalocker.data.__tweetedUrl = c.url;
    var g = a.pandalocker.tools.URL().scheme("http").host("twitter.com").path("/intent/tweet").query(e).toString(),
      h = 550, i = 420, j = screen.width ? screen.width / 2 - h / 2 + a.pandalocker.tools.findLeftWindowBoundry() : 0,
      k = screen.height ? screen.height / 2 - i / 2 + a.pandalocker.tools.findTopWindowBoundry() : 0;
    return a.pandalocker.data.__twitterAuth && a.pandalocker.data.__twitterAuth.closed === !1 ? (a.pandalocker.data.__twitterAuth.updateState(g, h, i, j, k), a.pandalocker.data.__tweetWindow = a.pandalocker.data.__twitterAuth, a.pandalocker.data.__twitterAuth = null) : a.pandalocker.data.__tweetWindow = window.open(g, "TwitterTweetWindow", "width=" + h + ",height=" + i + ",left=" + j + ",top=" + k), setTimeout(function () {
      var b = setInterval(function () {
        a.pandalocker.data.__tweetWindow && a.pandalocker.data.__tweetWindow.closed === !1 || (clearInterval(b), d.resolve())
      }, 200)
    }, 200), d.promise()
  }, b.connect = function (b) {
    var c = this;
    if (a.pandalocker.data.twitterOAuthReady) a.pandalocker.data.__twitterAuthIdentityData ? b(a.pandalocker.data.__twitterAuthIdentityData, c._getServiceData()) : this._identify(function (a) {
      b(a, c._getServiceData())
    }); else {
      var d = {opandaHandler: "twitter", opandaRequestType: "init", opandaKeepOpen: !0, opandaReadOnly: !0},
        e = a.pandalocker.tools.cookie("opanda_twid");
      e && "null" !== e && (d.opandaVisitorId = e);
      var f = c.options.proxy;
      for (var g in d) d.hasOwnProperty(g) && (f = a.pandalocker.tools.updateQueryStringParameter(f, g, d[g]));
      c._trackWindow("opandaHandler=twitter", function () {
        setTimeout(function () {
          a.pandalocker.data.twitterOAuthReady || (c.runHook("raw-social-app-declined"), c.showNotice(a.pandalocker.lang.errors_not_signed_in))
        }, 500)
      });
      var h = 500, i = 610,
        j = screen.width ? screen.width / 2 - h / 2 + a.pandalocker.tools.findLeftWindowBoundry() : 0,
        k = screen.height ? screen.height / 2 - i / 2 + a.pandalocker.tools.findTopWindowBoundry() : 0;
      a.pandalocker.data.__twitterAuth = window.open(f, "Twitter Tweet", "width=" + h + ",height=" + i + ",left=" + j + ",top=" + k + ",resizable=yes,scrollbars=yes,status=yes"), window.OPanda_TwitterOAuthCompleted = function (d) {
        a.pandalocker.data.twitterOAuthReady = !0, c._saveVisitorId(d), c.connect(b)
      }, window.OPanda_TwitterOAuthDenied = function (b) {
        c.runHook("raw-social-app-declined"), c.showNotice(a.pandalocker.lang.errors_not_signed_in), c._saveVisitorId(b)
      }
    }
  }, b._saveVisitorId = function (b) {
    this._visitorId = b, a.pandalocker.data.__twitterVisitorId = b, a.pandalocker.tools.cookie("opanda_twid", b, {
      expires: 1e3,
      path: "/"
    })
  }, b._getServiceData = function () {
    return {visitorId: a.pandalocker.data.__twitterVisitorId}
  }, b._identify = function (b) {
    var c = this, d = a.ajax({
      type: "POST",
      dataType: "json",
      url: c.options.proxy,
      data: {
        opandaHandler: "twitter",
        opandaRequestType: "user_info",
        opandaVisitorId: a.pandalocker.data.__twitterVisitorId,
        opandaReadOnly: !0
      },
      success: function (c) {
        console.log(c), (!c || c.error || c.errors) && console && console.log && console.log("Unable to get the user data: " + d.responseText);
        var e = {};
        e.displayName = c.screen_name, e.twitterUrl = "https://twitter.com/" + c.screen_name, c.profile_image_url && (e.image = c.profile_image_url.replace("_normal", "")), a.pandalocker.data.__twitterAuthIdentityData = e, b(e)
      },
      error: function () {
        console && console.log && console.log("Unable to get the user data: " + d.responseText), b({})
      }
    })
  }, b.checkTweet = function () {
    var b = this, c = a.Deferred(), d = a.ajax({
      type: "POST",
      dataType: "json",
      url: b.options.proxy,
      data: {
        opandaHandler: "twitter",
        opandaRequestType: "get_tweets",
        opandaVisitorId: a.pandalocker.data.__twitterVisitorId,
        opandaReadOnly: !0
      },
      success: function (a) {
        (!a || a.error || a.errors) && console && console.log && console.log("Unable to get the user data: " + d.responseText);
        for (var e = 0; e < a.length; e++) if (a[e].entities) for (var f = 0; f < a[e].entities.urls.length; f++) if (a[e].entities.urls[f] && a[e].entities.urls[f].expanded_url === b.url) return void c.resolve();
        c.reject()
      },
      error: function () {
        console && console.log && console.log("Unable to get the user data: " + d.responseText), callback({})
      }
    });
    return c.promise()
  }, a.pandalocker.controls["social-buttons"]["twitter-tweet"] = b
}(jQuery);
;
