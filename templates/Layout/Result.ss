<h2>Request result</h2>
<% loop $Data %>
    <% if $Heading %>
        <h3 class="level-$Level">$Key</h3>
    <% else %>
        <p class="level-$Level">
            <span class="key">$Key</span>
            => <span class="value">$Value</span>
        </p>
    <% end_if %>
<% end_loop %>
