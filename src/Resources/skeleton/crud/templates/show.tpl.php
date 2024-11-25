{% block page_content %}
    <div class="modal-header">
        <h4 class="modal-title">Détails <?= $entity_class_name ?></h4>
        <div class="btn btn-icon btn-sm  ms-2" data-bs-dismiss="modal" aria-label="Close">
            <span class="svg-icon svg-icon-2x text-white">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
					<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
				</svg>
            </span>
        </div>
    </div>
   
    <div class="modal-body">
        <table class="table table-bordered">
        <tbody>
            <?php foreach ($entity_fields as $field): ?>
                <tr>
                    <th><?= ucfirst($field['fieldName']) ?></th>
                    <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn dark btn-outline" data-bs-dismiss="modal">Fermer</button>
    </div>

{% endblock %}
{% block java %}
    <script>
        
    </script>
{% endblock %}