<link rel="stylesheet" href="../css/flatpickr.min.css">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .form-control[readonly] {
        background-color: #ffffff;
    }
    label::after {
        content: ":";
    }
    label.no-colon::after {
        content: "" !important;
    }
    .ql-toolbar {
        line-height: normal;
    }
    .ql-clear {
        display: inline-block;
        font-family: bootstrap-icons !important;
        font-style: normal;
        font-weight: normal !important;
        font-variant: normal;
        font-size: 1.1rem;
        text-transform: none;
        line-height: 1;
        vertical-align: -.125em;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .ql-clear::after {
        content: "\f38f";
    }
    .ql-clear:hover::after {
        content: "\f38e";
    }

    dt::after {
        content: ": ";
    }
</style>