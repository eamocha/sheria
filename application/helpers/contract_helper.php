<?php
if (!function_exists('get_contract_amendment_changes')) {
    /**
     * Compares only the specified contract fields between original and post data.
     *
     * @param array $original_contract Original contract fields.
     * @param array $post_data         Post data (e.g., from $this->input->post(NULL, true)).
     * @param array $fields_to_compare List of field names to compare.
     * @return array                   List of differences with keys: field_name, old_value, new_value.
     */
    function get_contract_amendment_changes($original, $new, $fields) {
        $changes = [];

        foreach ($fields as $field) {
            $old = $original[$field] ?? null;
            $newVal = $new[$field] ?? null;

            // Normalize
            $old = is_string($old) ? trim((string) $old) : $old;
            $newVal = is_string($newVal) ? trim((string) $newVal) : $newVal;

            // Convert date formats (example if needed)
            if (is_date_string($old) && is_date_string($newVal)) {
                $old = date('Y-m-d', strtotime($old));
                $newVal = date('Y-m-d', strtotime($newVal));
            }

            if ($old != $newVal) {
                $changes[] = [
                    "field_name" => $field,
                    "old_value" => $old,
                    "new_value" => $newVal,
                ];
            }
        }

        return $changes;
    }

    function is_date_string($str) {
        return preg_match("/\d{4}[-\/]\d{2}[-\/]\d{2}/", $str);
    }

}
