jQuery(function () {
    var feed = new Instafeed({
        get: 'user',
        userId: '_dream_white',
        filter: function(image) {
            return image.tags.indexOf('К473_11') >= 0;
        },
        accessToken: "3667045220.1677ed0.1ea1514c4c7c47ceb7ef497eff50b214",
        target: "instafeed"
    });
    feed.run();
});