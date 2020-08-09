{% extends 'templates/main.volt' %}

{% block content %}

    <div class="kg-nav">
        <div class="kg-nav-left">
        <span class="layui-breadcrumb">
            <a class="kg-back"><i class="layui-icon layui-icon-return"></i> 返回</a>
            {% if course %}
                <a><cite>{{ course.title }}</cite></a>
            {% endif %}
            <a><cite>评价管理</cite></a>
        </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ url({'for':'admin.review.search'}) }}">
                <i class="layui-icon layui-icon-search"></i>搜索评价
            </a>
        </div>
    </div>

    <table class="layui-table kg-table layui-form">
        <colgroup>
            <col>
            <col>
            <col>
            <col width="10%">
            <col width="10%">
        </colgroup>
        <thead>
        <tr>
            <th>內容</th>
            <th>评分</th>
            <th>用户</th>
            <th>发布</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            <tr>
                <td>
                    <p>课程：<a href="{{ url({'for':'admin.review.list'},{'course_id':item.course.id}) }}">{{ item.course.title }}</a></p>
                    <p>评价：<a href="javascript:" title="{{ item.content }}">{{ substr(item.content,0,30) }}</a></p>
                    <p>时间：{{ date('Y-m-d H:i:s',item.create_time) }}</p>
                </td>
                <td>
                    <p>内容实用：{{ item.rating1 }}</p>
                    <p>通俗易懂：{{ item.rating2 }}</p>
                    <p>逻辑清晰：{{ item.rating3 }}</p>
                </td>
                <td>
                    <p>昵称：<a href="{{ url({'for':'admin.review.list'},{'owner_id':item.owner.id}) }}">{{ item.owner.name }}</a></p>
                    <p>编号：{{ item.owner.id }}</p>
                </td>
                <td><input type="checkbox" name="published" value="1" lay-skin="switch" lay-text="是|否" lay-filter="published" data-url="{{ url({'for':'admin.review.update','id':item.id}) }}" {% if item.published == 1 %}checked{% endif %}></td>
                <td align="center">
                    <div class="layui-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <span class="layui-icon layui-icon-triangle-d"></span></button>
                        <ul>
                            <li><a href="{{ url({'for':'admin.review.edit','id':item.id}) }}">编辑</a></li>
                            {% if item.deleted == 0 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ url({'for':'admin.review.delete','id':item.id}) }}">删除</a></li>
                            {% else %}
                                <li><a href="javascript:" class="kg-restore" data-url="{{ url({'for':'admin.review.restore','id':item.id}) }}">还原</a></li>
                            {% endif %}
                        </ul>
                    </div>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}