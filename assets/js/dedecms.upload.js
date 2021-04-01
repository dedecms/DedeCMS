"use strict";

let albumIdData = [];
let albumUploadData = [];
let fileSize = 2;

// 页面加载完成
$(document).ready(function () {
    dedecmsAlbumDataUpdate();
});

// 图集 删除
function dedecmsAlbumDelete(that) {
    let div = $(that).parent().parent().parent().parent();
    let id = div.attr("id");

    $(`#${id} .uk-card-body input:hidden`).each(function () {
        div.after($(this));
    });

    div.remove();
}

// 图集 编辑
function dedecmsAlbumEdit(that) {
    let files = $(that).prop("files");

    if (files) {
        files = Array.from(files);

        let file = files[0];

        if (file === undefined) {
            return;
        }

        if (/(image\/)/i.test(file.type)) {
            let reader = new FileReader();

            reader.onload = function () {
                let img = $(that).next();
                let input = $(that).parent().next().children().children("input");
                let src = this.result;
                let remark = file.name.substring(0, file.name.lastIndexOf("."));

                img.attr("src", src);
                input.val(remark);
                input.attr("value", remark);
            }

            reader.readAsDataURL(file);
        }
    }
}

// 图集 移动
UIkit.util.on(document, "moved", "#dedecms-album", function (item) {
    dedecmsAlbumDataUpdate();
});

// 图集 数据更新
function dedecmsAlbumDataUpdate() {
    let albumIds = $("#dedecms-album-ids");

    albumIdData = [];
    $(".dedecms-album-id").each(function () {
        let id = $(this).attr("id");
        id = id.replace("albumId", "");

        if (albumIdData.indexOf(id) === -1) {
            albumIdData.push(id);
        }
    });

    albumIds.val(JSON.stringify(albumIdData));
}

// 图集 预览
function dedecmsAlbumPreview(that) {
    let files = $(that).prop("files");
    let albumPreview = $("#dedecms-album-preview");
    let albumPreviewProgress = $("#dedecms-album-preview-progress");
    let albumUploadFiles = $("#dedecms-album-upload-files");

    if (files) {
        files = Array.from(files);

        $.each(files, function (i, file) {
            if (file === undefined) {
                return;
            }

            if (file.size > fileSize * 1024 * 1024) {
                UIkit.notification({
                    message: `[${file.name}] 图片大小超过${fileSize}MB`,
                    status: "danger",
                    pos: "bottom-center",
                    timeout: 10000
                });
                return;
            }

            if (/(image\/)/i.test(file.type)) {
                let id = "" + (new Date()).valueOf() + random(1000, 9999);
                let html = `
                <div id="${id}">
                    <input type="hidden" class="dedecms-album-preview-name">
                    <input type="hidden" class="dedecms-album-preview-remark">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-header uk-padding-small">
                            <div class="uk-flex uk-flex-between uk-invisible">
                                <span class="uk-sortable-handle" uk-icon="icon: arrows-move"></span>
                                <button type="button" uk-close onclick="dedecmsAlbumPreviewDelete(this)"></button>
                            </div>
                        </div>
                        <div class="uk-card-body uk-padding-remove uk-text-center" uk-form-custom>
                            <input type="file" accept="image/*" onchange="dedecmsAlbumPreviewEdit(this)" disabled>
                            <img class="uk-height-small">
                            <div>图片上传中...</div>
                        </div>
                        <div class="uk-card-footer uk-padding-small">
                            <progress class="uk-progress uk-margin-small-bottom" max="100"></progress>
                            <div class="uk-flex uk-flex-middle">
                                <label class="uk-form-label uk-width-auto uk-margin-small-right">注释</label>
                                <input class="uk-input uk-form-small uk-width-expand" type="text" value="图片上传中..." onkeyup="dedecmsAlbumPreviewEdit(this)" disabled>
                            </div>
                        </div>
                    </div>
                </div>`;

                albumPreview.append(html);

                let reader = new FileReader();
                reader.onload = function () {
                    $(`#${id} img`).attr("src", this.result);
                }
                reader.readAsDataURL(file);

                let formData = new FormData();
                formData.append("file", file);

                $.ajax({
                    method: "POST",
                    url: "upload.php",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false
                }).always(function (res) {
                    if (res.status === "success") {
                        let name = res.name;
                        let remark = res.remark;

                        $(`#${id} .dedecms-album-preview-name`).val(name);
                        $(`#${id} .dedecms-album-preview-remark`).val(remark);
                        $(`#${id} .uk-card-body div`).html("图片上传成功");
                        $(`#${id} progress`).val(100);
                        $(`#${id} .uk-card-footer input`).val(remark);
                        $(`#${id} .uk-card-footer input`).attr("value", remark);

                        albumUploadData.push({
                            "name": name,
                            "remark": remark
                        });

                        albumUploadFiles.val(JSON.stringify(albumUploadData));
                    } else {
                        $(`#${id} .dedecms-album-preview-name`).val("");
                        $(`#${id} .dedecms-album-preview-remark`).val("");
                        $(`#${id} .uk-card-body div`).html("图片上传失败");
                        $(`#${id} progress`).val(0);
                        $(`#${id} .uk-card-footer input`).val("图片上传失败");
                        $(`#${id} .uk-card-footer input`).attr("value", "图片上传失败");
                    }

                    let index = 0;
                    let length = 0;
                    $("#dedecms-album-preview .uk-card-body div").each(function () {
                        if ($(this).html() !== "图片上传中...") {
                            index++;
                        }
                        length++;
                    });
                    albumPreviewProgress.removeClass("uk-hidden");
                    albumPreviewProgress.children("progress").attr("value", index);
                    albumPreviewProgress.children("progress").attr("max", length);
                    if (index === length) {
                        UIkit.notification({
                            message: "图片上传完毕",
                            status: "success",
                            pos: "bottom-center",
                            timeout: 10000
                        });
                        $(".uk-card-header div").removeClass("uk-invisible");
                        $(".uk-card-body input").removeAttr("disabled");
                        $(".uk-card-body div").html("点击图片进行修改");
                        $(".uk-card-footer input").each(function () {
                            if ($(this).val() !== "图片上传失败") {
                                $(this).removeAttr("disabled");
                            }
                        });

                        dedecmsAlbumUploadDataUpdate();
                    }
                });
            }
        });
    }

    $(that).after($(that).clone().val(""));
    $(that).remove();
}

// 图集 预览 删除
function dedecmsAlbumPreviewDelete(that) {
    let div = $(that).parent().parent().parent().parent();
    let name = div.children(".dedecms-album-preview-name").val();
    let remark = div.children(".dedecms-album-preview-remark").val();
    let albumUploadFiles = $("#dedecms-album-upload-files");

    if (name === "" || remark === "") {
        div.remove();
    } else {
        let formData = new FormData();
        formData.append("dopost", "delete");
        formData.append("delete", name);

        $.ajax({
            method: "POST",
            url: "upload.php",
            data: formData,
            processData: false,
            contentType: false
        }).always(function (res) {
            if (res === "success") {
                albumUploadData = objArrayRemove(albumUploadData, "name", name);

                albumUploadFiles.val(JSON.stringify(albumUploadData));

                div.remove();
            } else {
                UIkit.notification({
                    message: "图片删除失败",
                    status: "danger",
                    pos: "bottom-center",
                    timeout: 10000
                });
            }
        });
    }
}

// 图集 预览 编辑
function dedecmsAlbumPreviewEdit(that) {
    let files = $(that).prop("files");

    if (files) {
        files = Array.from(files);

        let file = files[0];

        if (file === undefined) {
            return;
        }

        if (file.size > fileSize * 1024 * 1024) {
            UIkit.notification({
                message: `[${file.name}] 图片大小超过${fileSize}MB`,
                status: "danger",
                pos: "bottom-center",
                timeout: 10000
            });
            return;
        }

        if (/(image\/)/i.test(file.type)) {
            let div = $(that).parent().parent().parent();
            let name = div.children(".dedecms-album-preview-name").val();
            let id = div.attr("id");

            $(`#${id} .dedecms-album-preview-name`).val("");
            $(`#${id} .dedecms-album-preview-remark`).val("");
            $(`#${id} .uk-card-header div`).addClass("uk-invisible");
            $(`#${id} .uk-card-body input`).attr("disabled", "disabled");
            $(`#${id} .uk-card-body div`).html("图片上传中...");
            $(`#${id} progress`).val(0);
            $(`#${id} .uk-card-footer input`).val("图片上传中...");
            $(`#${id} .uk-card-footer input`).attr("value", "图片上传中...");
            $(`#${id} .uk-card-footer input`).attr("disabled", "disabled");

            let reader = new FileReader();
            reader.onload = function () {
                $(`#${id} img`).attr("src", this.result);
            }
            reader.readAsDataURL(file);

            let formData = new FormData();
            formData.append("file", file);
            formData.append("delete", name);

            $.ajax({
                method: "POST",
                url: "upload.php",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false
            }).always(function (res) {
                if (res.status === "success") {
                    let name = res.name;
                    let remark = res.remark;

                    $(`#${id} .dedecms-album-preview-name`).val(name);
                    $(`#${id} .dedecms-album-preview-remark`).val(remark);
                    $(`#${id} .uk-card-body div`).html("图片上传成功");
                    $(`#${id} progress`).val(100);
                    $(`#${id} .uk-card-footer input`).val(remark);
                    $(`#${id} .uk-card-footer input`).attr("value", remark);
                } else {
                    $(`#${id} .dedecms-album-preview-name`).val("");
                    $(`#${id} .dedecms-album-preview-remark`).val("");
                    $(`#${id} .uk-card-body div`).html("图片上传失败");
                    $(`#${id} progress`).val(0);
                    $(`#${id} .uk-card-footer input`).val("图片上传失败");
                    $(`#${id} .uk-card-footer input`).attr("value", "图片上传失败");
                }

                $(`#${id} .uk-card-header div`).removeClass("uk-invisible");
                $(`#${id} .uk-card-body input`).removeAttr("disabled");
                $(`#${id} .uk-card-body div`).html("点击图片进行修改");
                $(`#${id} .uk-card-footer input`).each(function () {
                    if ($(this).val() !== "图片上传失败") {
                        $(this).removeAttr("disabled");
                    }
                });

                dedecmsAlbumUploadDataUpdate();

                $(that).after($(that).clone().val(""));
                $(that).remove();
            });
        }
    } else {
        let div = $(that).parent().parent().parent().parent();
        let id = div.attr("id");
        let remark = $(that).val();

        $(`#${id} .dedecms-album-preview-remark`).val(remark);
        $(`#${id} .uk-card-footer input`).val(remark);
        $(`#${id} .uk-card-footer input`).attr("value", remark);

        dedecmsAlbumUploadDataUpdate();
    }
}

// 图集 预览 移动
UIkit.util.on(document, "moved", "#dedecms-album-preview", function (item) {
    dedecmsAlbumUploadDataUpdate();
});

// 图集 上传数据更新
function dedecmsAlbumUploadDataUpdate() {
    $(document).ready(function () {
        let name = [];
        let remark = [];
        let albumUploadFiles = $("#dedecms-album-upload-files");

        $(".dedecms-album-preview-name").each(function () {
            name.push($(this).val());
        });
        $(".dedecms-album-preview-remark").each(function () {
            remark.push($(this).val());
        });

        albumUploadData = [];
        for (let i = 0; i < name.length; i++) {
            if (name[i] === "" || remark[i] === "") {
                continue;
            }

            albumUploadData.push({
                "name": name[i],
                "remark": remark[i]
            });
        }

        albumUploadFiles.val(JSON.stringify(albumUploadData));
    });
}