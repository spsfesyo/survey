<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.checkbox-wrapper').forEach(function(wrapper) {
            const batas = parseInt(wrapper.dataset.batasPilihan);
            const id = wrapper.dataset.questionId;
            const checkboxes = wrapper.querySelectorAll('.checkbox-group-' + id);

            if (!batas || checkboxes.length === 0) return;

            function enforceLimit() {
                const checked = Array.from(checkboxes).filter(cb => cb.checked);
                if (checked.length >= batas) {
                    checkboxes.forEach(cb => {
                        if (!cb.checked) cb.disabled = true;
                    });
                } else {
                    checkboxes.forEach(cb => cb.disabled = false);
                }
            }

            checkboxes.forEach(cb => cb.addEventListener('change', enforceLimit));
            enforceLimit();
        });

        document.querySelector('form')?.addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('input[name="required_checkbox[]"]').forEach(function(input) {
                const id = input.value;
                const wrapper = document.querySelector(
                    `.checkbox-wrapper[data-question-id="${id}"]`);
                const batas = parseInt(wrapper?.dataset.batasPilihan || 0);
                const checkboxes = wrapper?.querySelectorAll(`.checkbox-group-${id}`);
                const checkedCount = Array.from(checkboxes ?? []).filter(cb => cb.checked)
                    .length;

                if (batas && checkedCount < batas) {
                    isValid = false;
                    alert(
                        `Pertanyaan membutuhkan minimal ${batas} pilihan. Anda baru memilih ${checkedCount}.`
                    );
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
