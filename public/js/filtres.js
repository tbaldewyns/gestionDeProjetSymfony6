window.onload = () => {
    const FiltersFrom = document.querySelector(".searchForm")

    document.querySelectorAll(".searchForm select").forEach(select => {
        select.addEventListener("change", () =>{
            const form = new FormData(FiltersFrom);

            const params = new URLSearchParams();
            form.forEach((value, key) => {
                params.append (key, value)
            });

            const url = new URL(window.location.href);

            fetch(url.pathname + "?" +params.toString() + "&ajax=1", {
                headers : {
                    "X-Requested-With":"XMLHttpRequest"
                }
            }).then(Response => {
                console.log(Response)
            }).catch(e => alert(e));
        })
    })
}