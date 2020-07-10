layui.use(['jquery', 'form', 'layer', 'helper'], function () {

    var $ = layui.jquery;
    var form = layui.form;
    var layer = layui.layer;
    var helper = layui.helper;

    var interval = null;
    var intervalTime = 15000;
    var userId = window.koogua.user.id;
    var requestId = helper.getRequestId();
    var chapterId = $('input[name="chapter.id"]').val();
    var planId = $('input[name="chapter.plan_id"]').val();
    var lastPosition = $('input[name="chapter.position"]').val();
    var learningUrl = $('input[name="chapter.learning_url"]').val();
    var danmuListUrl = $('input[name="chapter.danmu_url"]').val();
    var playUrls = JSON.parse($('input[name="chapter.play_urls"]').val());
    var $danmuText = $('input[name="danmu.text"]');

    var options = {
        autoplay: false,
        width: 760,
        height: 428
    };

    if (playUrls.od) {
        options.m3u8 = playUrls.od.url;
    }

    if (playUrls.hd) {
        options.m3u8_hd = playUrls.hd.url;
    }

    if (playUrls.sd) {
        options.m3u8_sd = playUrls.sd.url;
    }

    options.listener = function (msg) {
        if (msg.type === 'play') {
            play();
        } else if (msg.type === 'pause') {
            pause();
        } else if (msg.type === 'ended') {
            ended();
        }
    };

    var player = new TcPlayer('player', options);

    var position = parseInt(lastPosition);

    /**
     * 过于接近结束位置当作已结束处理
     */
    if (position > 0 && player.duration() - position > 10) {
        player.currentTime(position);
    }

    $('#danmu').danmu({
        left: 20,
        top: 20,
        width: 750,
        height: 380
    });

    initDanmu();

    form.on('checkbox(danmu.status)', function (data) {
        if (data.elem.checked) {
            $('#danmu').danmu('setOpacity', 1);
        } else {
            $('#danmu').danmu('setOpacity', 0);
        }
    });

    form.on('submit(danmu.send)', function (data) {
        $.ajax({
            type: 'POST',
            url: data.form.action,
            data: {
                text: $danmuText.val(),
                time: player.currentTime(),
                chapter_id: chapterId
            },
            success: function (res) {
                $('#danmu').danmu('addDanmu', {
                    text: res.danmu.text,
                    color: res.danmu.color,
                    size: res.danmu.size,
                    time: (res.danmu.time + 1) * 10, //十分之一秒
                    position: res.danmu.position,
                    isnew: 1
                });
                $danmuText.val('');
            },
            error: function (xhr) {
                var res = JSON.parse(xhr.responseText);
                layer.msg(res.msg, {icon: 2});
            }
        });
        return false;
    });

    function clearLearningInterval() {
        if (interval != null) {
            clearInterval(interval);
            interval = null;
        }
    }

    function setLearningInterval() {
        interval = setInterval(learning, intervalTime);
    }

    function play() {
        startDanmu();
        clearLearningInterval();
        setLearningInterval();
    }

    function pause() {
        /**
         * 视频结束也会触发暂停事件，此时弹幕可能尚未结束
         * 时间差区分暂停是手动还是结束触发
         */
        if (player.currentTime() < player.duration() - 5) {
            pauseDanmu();
        }
        clearLearningInterval();
    }

    function ended() {
        clearLearningInterval();
        learning();
    }

    function learning() {
        if (userId !== '0' && planId !== '0') {
            $.ajax({
                type: 'POST',
                url: learningUrl,
                data: {
                    plan_id: planId,
                    chapter_id: chapterId,
                    request_id: requestId,
                    interval: intervalTime,
                    position: player.currentTime()
                }
            });
        }
    }

    function startDanmu() {
        $('#danmu').danmu('danmuResume');
    }

    function pauseDanmu() {
        $('#danmu').danmu('danmuPause');
    }

    /**
     * 一次性获取弹幕，待改进为根据时间轴区间获取
     */
    function initDanmu() {
        $.ajax({
            type: 'GET',
            url: danmuListUrl,
            success: function (res) {
                var items = [];
                layui.each(res.items, function (index, item) {
                    items.push({
                        text: item.text,
                        color: item.color,
                        size: item.size,
                        time: (item.time + 1) * 10,
                        position: item.position
                    });
                });
                $('#danmu').danmu('addDanmu', items);
            }
        });
    }

});