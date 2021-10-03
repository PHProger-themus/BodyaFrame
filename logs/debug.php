{{ header }}
<div id="product-category" class="container">

    <!--<ul class="breadcrumb">
          {% for breadcrumb in breadcrumbs %}
          <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>
          {% endfor %}
      </ul>-->

    <div class="main_breadcrumb">
        <div class="">
            <ul class="breadcrumb">
                {% for breadcrumb in breadcrumbs %}
                <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>
                {% endfor %}
            </ul>
        </div>
    </div>

    {% if heading_description %}
    {% if del_h1 %}<h1 class="title_pages hidden_h1">{{ heading_title }}</h1>{% endif %}
    <div class="row">{{ heading_description }}</div>
    {% else %}
    <h1 class="title_pages">{{ heading_title }}</h1>
    {% endif %}

    <span class="mob">{{ mobile }}</span>
    <span class="is_main_category">{{ ismc }}</span>
    <div class="row" id="prod_catalog">{{ column_left }}
        {% if column_left and column_right %}
        {% set class = 'col-sm-6' %}
        {% elseif column_left or column_right %}
        {% set class = 'col-sm-9' %}
        {% else %}
        {% set class = 'col-sm-12' %}
        {% endif %}
        {% if has_menu == true %}
        <div class="tmenu d_ajax_filter">
            <div class="af-body"></div>
        </div>
        {% endif %}
        <div id="content" class="{{ class }}">{{ content_top }}


            <hr>
            {% if categories %}
            <h3>{{ text_refine }}</h3>
            {% if categories|length <= 5 %}
            {% for category in categories %}
            <h3><a href="{{ category.href }}">{{ category.name }}</a></h3>
            {% endfor %}
            {% else %}
            <div class="row">{% for category in categories|batch((categories|length / 4)|round(1, 'ceil')) %}
                <div class="col-sm-3">
                    <ul>
                        {% for child in category %}
                        <li><a href="{{ child.href }}">{{ child.name }}</a></li>
                        {% endfor %}
                    </ul>
                </div>
                {% endfor %}
            </div>
            <br/>
            {% endif %}
            {% endif %}
            {% if products %}

            <div class="row category_grid-wrapper">
                <div class="col-md-2 col-sm-6 hidden-xs">
                    <div class="btn-group btn-group-sm">
                        <button type="button" id="grid-view" class="btn btn-default active" data-toggle="tooltip"
                                title="" data-original-title="Таблица"><i class="fa fa-th"></i></button>
                        <button type="button" id="list-view" class="btn btn-default" data-toggle="tooltip" title=""
                                data-original-title="Список"><i class="fa fa-th-list"></i></button>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group"><a href="{{ compare }}" id="compare-total" class="btn btn-link">{{
                            text_compare }}</a></div>
                </div>
                <div class="col-md-4 col-xs-6">
                    <div class="form-group input-group input-group-sm">
                        <label class="input-group-addon" for="input-sort">Сортировка:</label>
                        <select id="input-sort" class="form-control" onchange="location = this.value;">
                            {% for sorts in sorts %}
                            {% if sorts.value == '%s-%s'|format(sort, order) %}


                            <option value="{{ sorts.href }}" selected="selected">{{ sorts.text }}</option>


                            {% else %}


                            <option value="{{ sorts.href }}">{{ sorts.text }}</option>


                            {% endif %}
                            {% endfor %}
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="form-group input-group input-group-sm">
                        <label class="input-group-addon" for="input-limit">Показать:</label>
                        <select id="input-limit" class="form-control" onchange="location = this.value;">
                            {% for limits in limits %}
                            {% if limits.value == limit %}


                            <option value="{{ limits.href }}" selected="selected">{{ limits.text }}</option>


                            {% else %}


                            <option value="{{ limits.href }}">{{ limits.text }}</option>


                            {% endif %}
                            {% endfor %}
                        </select>
                    </div>
                </div>
            </div>


            <div class="row"> {% for product in products %}

                <div class="product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    {% if product.stock == 7 %}
                    <div class="stock_stat_mn">В наличии</div>
                    {% endif %}
                    <div class="product-thumb transition">
                        <!--products _icons and Hover -->
                        <div class="hovereffect1">
                            <div class="image"><a href="{{ product.href }}"><img src="{{ product.thumb }}"
                                                                                 alt="{{ product.name }}"
                                                                                 title="{{ product.name }}"
                                                                                 class="img-responsive"></a></div>

                            <div class="overlay">
                                <p class="buy_btn">
                                    <button type="button" class="product_button"
                                            onclick="cart.add('{{ product.product_id }}', '{{ product.minimum }}');">
                                        <span class=""><i class="fa fa-shopping-basket"></i> В КП</span></button>
                                </p>
                                <div class="icon-links">

                                    <div>
                                        <button type="button" class="product_button" data-toggle="tooltip" title=""
                                                onclick="wishlist.add('{{ product.product_id }}');"
                                                data-original-title="Добавить в закладки"><i class="fa fa-heart"></i>
                                        </button>
                                    </div>

                                    <div>
                                        <button type="button" class="product_button" data-toggle="tooltip" title=""
                                                onclick="compare.add('{{ product.product_id }}');"
                                                data-original-title="Сравнить"><i class="fa fa-random"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- products text matter  -->
                        <div class="caption products_text_matter">

                            <!--review_status -->
                            <div class="rating"> {% for i in 1..5 %}
                                {% if product.rating < i %} <span class="fa fa-stack"><i
                                            class="fa fa-star-o fa-stack-2x"></i></span> {% else %} <span
                                        class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i
                                            class="fa fa-star-o fa-stack-2x"></i></span>{% endif %}
                                {% endfor %}
                            </div>

                            <p class="product_name_blh"><a href="{{ product.href }}">{{ product.name }}</a></p>


                            <p class="price"> {% if not product.special %}
                                {{ product.price_visual }}
                                {% else %} <span class="price-new">{{ product.special }}</span> <span class="price-old">{{ product.price }}</span>
                                {% endif %}

                                <!-- grid button style  -->
                            <div class="grid_button_wrapper" style="display: none;">
                                <button type="button" onclick="cart.add('{{ product.product_id }}');"><span class=""><i
                                                class="fa fa-shopping-basket"></i>{{ button_cart }}</span></button>
                                <div class="quick_view_icons_wrapper">
                                    <div class="quick_view_icones">
                                        <a href="{{ product.href }}" class="quickviewopen">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                    <div>
                                        <button type="button" data-toggle="tooltip" title=""
                                                onclick="wishlist.add('{{ product.product_id }}');"
                                                data-original-title="Add to Wish List"><i class="fa fa-heart"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" data-toggle="tooltip" title=""
                                                onclick="compare.add('{{ product.product_id }}');"
                                                data-original-title="Compare this Product"><i class="fa fa-random"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {% endfor %}
            </div>
            <div class="row">
                <div class="col-sm-6 text-left">{{ pagination }}</div>
                <div class="col-sm-6 text-right">{{ results }}</div>
            </div>
            {% endif %}
            {% if not categories and not products %}
            <p>{{ text_empty }}</p>
            <div class="buttons">
                <div class="pull-right"><a href="{{ continue }}" class="btn btn-primary">{{ button_continue }}</a></div>
            </div>
            {% endif %}
            {{ content_bottom }}
        </div>
        {{ column_right }}
    </div>

    {% if description_on_page %}
    <div class="description_mpr">{{ description }}</div>
    {% endif %}

    {% if bottom_description %}
    <div class="row">{{ bottom_description }}</div>
    {% endif %}

</div>
{{ footer }}
